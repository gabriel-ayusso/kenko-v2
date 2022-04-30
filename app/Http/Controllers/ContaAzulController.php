<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContaAzulController extends Controller
{
    private $clientId = "zaOIZZf2ixepZlERriqTXZ2vIINhlyQZ";
    private $clientSecret = "z3aXSRBuFoj1qtFyW0YVy11CQvy4SDg7";
    private $state = "DCEeFWf45A53sdfKef424";
    private $scope = "sales";
    private $redirectUri = "https://agenda.kenkostudio.com.br/conta-azul/login-callback";
    private $caApiUrl = "https://api.contaazul.com";
    private $caAuthUrl = "https://api.contaazul.com/auth/authorize";
    private $caTokenUrl = "https://api.contaazul.com/oauth2/token";

    private function getToken()
    {
        $dbObj = Setting::where('key', 'CA_ACCESS_TOKEN')->first();
        return $dbObj['value'];
    }

    private function getPkce()
    {
        $code = "gabriel_ayusso_guimaraes";
        $hash = hash('sha256', $code);
        $hashedCoce = base64_encode($hash);
        return $hashedCoce;
    }

    private function createCaService(Service $service)
    {
        Log::info(">>> criando o serviço $service->name");
        $token = $this->getToken();
        $resp = Http::withToken($token)->post("$this->caApiUrl/v1/services", [
            'name' => $service->name,
            'type' => 'PROVIDED',
            'value' => $service->price,
            'cost' => $service->price,
        ]);

        $json = $resp->json();
        if ($resp->failed()) {
            Log::error(">>> Falha ao tentar incluir o serviço: ", ['json' => $json]);
            return null;
        }

        $sql = "insert into ca_services (id, name, value, cost) values (:id, :name, :value, :cost) on duplicate key update name = :name, value = :value, cost = :cost";
        DB::insert($sql, [
            'id' => $json['id'],
            'name' => $json['name'],
            'value' => $json['value'],
            'cost' => $json['cost'],
        ]);

        Log::info('>>> serviço criado:', ['service' => $json]);
        return $json['id'];
    }

    private function createCaCustomer(Booking $booking)
    {
        Log::info(">>> criando o cliente $booking->name");
        $token = $this->getToken();
        $resp = Http::withToken($token)->post("$this->caApiUrl/v1/customers", [
            'name' => $booking->name,
            'person_type' => 'NATURAL',
            'email' => $booking->email,
            'business_phone' => Helper::removeFormats($booking->phone),
        ]);

        $json = $resp->json();
        if ($resp->failed()) {
            Log::error(">>> Falha ao tentar incluir o cliente: ", ['json' => $json]);
            return null;
        }

        $sql = "insert into ca_customers (id, name, email, phone) values (:id, :name, :email, :phone) on duplicate key update name = :name, email = :email, phone = :phone";
        DB::insert($sql, [
            'id' => $json['id'],
            'name' => $json['name'],
            'email' => $json['email'],
            'phone' => $json['business_phone'],
        ]);

        Log::info('>>> cliente criado:', ['service' => $json]);
        return $json['id'];
    }

    public function refreshToken()
    {
        $refreshToken = Setting::where('key', 'CA_REFRESH_TOKEN')->first()['value'];
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->caTokenUrl, [
                'refresh_token' =>  $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

        $body = $response->json();
        if ($response->failed()) {
            return view('conta-azul.login-failed', ['message' => $body['error_description']]);
        }


        $accessToken =  $body['access_token'];
        $refreshToken =  $body['refresh_token'];
        $now = Carbon::now();

        DB::update("insert into settings (`key`, value) values (?, ?) on duplicate key update value = ?", ['CA_ACCESS_TOKEN',  $accessToken, $accessToken]);
        DB::update("insert into settings (`key`, value) values (?, ?) on duplicate key update value = ?", ['CA_REFRESH_TOKEN', $refreshToken, $refreshToken]);
        DB::update("insert into settings (`key`, value) values (:key, :value) on duplicate key update value = :value", ['key' => 'CA_LAST_UPDATE', 'value' => $now]);

        return redirect()->route('conta-azul.index', ['message' => 'token renovado com sucesso']);
    }

    public function index(Request $request)
    {
        Gate::authorize('conta-azul');
        $message = $request->message;

        $codeChallenge = self::getPkce();
        $authUrl = "$this->caAuthUrl?response_type=code&client_id=$this->clientId&state=$this->state&scope=$this->scope&redirect_uri=$this->redirectUri&code_challenge=$codeChallenge&code_challenge_method=S256";

        $countServices = DB::scalar("select count(1) from ca_services");
        $countCustomers = DB::scalar("select count(1) from ca_customers");
        $countBanks = DB::scalar("select count(1) from ca_banks");
        $tokenLastUpdate = DB::scalar("select value from settings where `key` = 'CA_LAST_UPDATE'");

        return view('conta-azul.index', [
            'authUrl' => $authUrl,
            'message' => $message,
            'countServices' => $countServices,
            'countCustomers' => $countCustomers,
            'countBanks' => $countBanks,
            'tokenLastUpdate' => $tokenLastUpdate,
        ]);
    }

    public function loginCallback(Request $request)
    {
        $code = $request->query('code');

        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'client_id' => "zaOIZZf2ixepZlERriqTXZ2vIINhlyQZ",
            'code_verifier' => 'gabriel_ayusso_guimaraes'
        ];

        $response = Http::withBasicAuth(
            $this->clientId,
            $this->clientSecret
        )
            ->asForm()
            ->post($this->caTokenUrl, $data);
        $body = $response->json();

        if ($response->failed()) {
            return view('conta-azul.login-failed', ['message' => $body['error_description']]);
        }

        $accessToken =  $body['access_token'];
        $refreshToken =  $body['refresh_token'];


        $now = Carbon::now();
        DB::update("insert into settings (`key`, value) values (?, ?) on duplicate key update value = ?", ['CA_ACCESS_TOKEN',  $accessToken, $accessToken]);
        DB::update("insert into settings (`key`, value) values (?, ?) on duplicate key update value = ?", ['CA_REFRESH_TOKEN', $refreshToken, $refreshToken]);
        DB::update("insert into settings (`key`, value) values (:key, :value) on duplicate key update value = :value", ['key' => 'CA_LAST_UPDATE', 'value' => $now]);

        return redirect()->route('conta-azul.index');
    }

    public function importServices()
    {
        $token = $this->getToken();


        $sql = "insert into ca_services (id, name, value, cost) values (:id, :name, :value, :cost) on duplicate key update name = :name, value = :value, cost = :cost";
        $total = 0;
        $page = 0;

        do {
            $response = Http::withToken($token)->get("$this->caApiUrl/v1/services?page=$page");
            $services = $response->json();
            if ($response->failed()) {
                return view('conta-azul.login-failed', ['message' => $services['error_description']]);
            }
            foreach ($services as $service) {
                DB::insert($sql, [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'value' => $service['value'],
                    'cost' => $service['cost'],
                ]);
                $total++;
            }
            $page++;
        } while (count($services) > 0);

        return redirect()->route('conta-azul.index', ['message' => "$total serviços importados com sucesso."]);
    }

    public function importBanks()
    {
        $token = $this->getToken();
        $response = Http::withToken($token)->get("$this->caApiUrl/v1/sales/banks");
        $json = $response->json();
        $total = 0;

        if ($response->failed()) {
            return view('conta-azul.login-failed', ['message' => $json['error_description']]);
        }

        $sql = "insert into ca_banks (id, name) values (:id, :name) on duplicate key update name = :name;";

        foreach ($json as $bank) {
            DB::insert($sql, ['id' => $bank['uuid'], 'name' => $bank['name']]);
            $total++;
        }

        return redirect()->route('conta-azul.index', ['message' => "$total bancos importados com sucesso."]);
    }

    public function importCustomers()
    {
        $token = $this->getToken();

        $sql = "insert into ca_customers (id, name, email, phone) values (:id, :name, :email, :phone) on duplicate key update name = :name, email = :email, phone = :phone";
        $total = 0;
        $page = 0;

        do {
            $response = Http::withToken($token)->get("$this->caApiUrl/v1/customers?page=$page");
            $customers = $response->json();
            if ($response->failed()) {
                return view('conta-azul.login-failed', ['message' => $customers['error_description']]);
            }
            foreach ($customers as $customer) {
                DB::insert($sql, [
                    'id' => $customer['id'],
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'phone' => Helper::removeFormats($customer['business_phone']),
                ]);
                $total++;
            }
            $page++;
        } while (count($customers) > 0);

        return redirect()->route('conta-azul.index', ['message' => "$total clientes importados com sucesso."]);
    }

    public function sales(Request $request)
    {
        $message = $request->message;
        $dt = $request->query('dt');
        if ($dt)
            $dt = Carbon::parse($dt);
        else
            $dt = Carbon::parse('today');

        $bookings = Booking::with(['employee', 'service'])->whereNull('ca_sale_id')->whereRaw("date(`date`) = ?", [$dt])->orderByDesc('date')->get();
        // $customers = DB::select("select id, name, email, phone from ca_customers order by name");

        return view('conta-azul.sales', ['dt' => $dt, 'bookings' => $bookings, 'message' => $message]);
    }

    public function salesPost(Request $request)
    {
        $selectedIds = $request->input('id');
        // $selectedCustomers = $request->input('ca_id');
        if ($selectedIds == null) {
            return redirect()->route('conta-azul.sales', ['message' => 'Nenhum agendmento selecionado']);
        }
        // foreach ($selectedCustomers as $selCustomer) {
        //     if ($selCustomer != null) {
        //         $array = explode(',', $selCustomer);
        //         DB::update("update bookings set ca_customer_id = :ca_id where id = :id", ['id' => $array[0], 'ca_id' => $array[1]]);
        //     }
        // }

        $services = DB::select("select id, name from ca_services");
        $customers = DB::select("select id, name, email, phone from ca_customers");
        $bookings = Booking::whereIn('id', $selectedIds)->get();
        foreach ($bookings as $booking) {
            $idx = array_search($booking->service->name, array_column($services, 'name'));
            if ($idx) {
                DB::update("update bookings set ca_service_id = :service_id where id = :id", ['id' => $booking->id, 'service_id' => $services[$idx]->id]);
            } else {
                DB::update("update bookings set ca_service_id = null where id = :id", ['id' => $booking->id]);
            }

            foreach ($customers as $customer) {
                if (
                    $customer->name == $booking->name ||
                    (!empty($booking->phone) && $customer->phone == Helper::removeFormats($booking->phone)) ||
                    (!empty($booking->email) && $customer->email == $booking->email)
                ) {
                    Log::debug('>>> Found customer for booking', ['booking' => $booking, 'customer' => $customer]);
                    DB::update("update bookings set ca_customer_id = :customer_id where id = :id", ['id' => $booking->id, 'customer_id' => $customer->id]);
                    break;
                }
            }
        }

        $bookings = Booking::with('caCustomer', 'caService', 'service', 'employee')->whereIn('id', $selectedIds)->orderBy('date')->get();
        $banks = DB::select("select id, name from ca_banks order by 2;");
        $lastBankId = DB::scalar("select value from settings where `key` = 'CA_LAST_BANK'");

        return view('conta-azul.sales-validation', ['bookings' => $bookings, 'banks' => $banks, 'lastBankId' => $lastBankId]);
    }

    public function syncBookings(Request $request)
    {
        $now = Carbon::now();
        $ids = $request->input("id");
        $bankId = $request->input('bank_id');

        if ($bankId) {
            DB::update("insert into settings (`key`, value) values (:key, :value) on duplicate key update value = :value", ['key' => 'CA_LAST_BANK', 'value' => $bankId]);
        }

        $token = $this->getToken();

        $bookings = Booking::with('service')->whereIn('id', $ids)->get();
        $results = [];
        foreach ($bookings as $booking) {
            $serviceId = $booking->ca_service_id;
            $customerId = $booking->ca_customer_id;

            if (!$serviceId) {
                $serviceId = $this->createCaService($booking->service);
                if ($serviceId) {
                    DB::update("update bookings set ca_service_id = :serviceId where id = :id", ['id' => $booking->id, 'serviceId' => $serviceId]);
                }
            }

            if (!$customerId) {
                $customerId = $this->createCaCustomer($booking);
                if ($serviceId) {
                    DB::update("update bookings set ca_customer_id = :customerId where id = :id", ['id' => $booking->id, 'customerId' => $customerId]);
                }
            }

            if (!$serviceId) {
                $results[] = [
                    'success' => false,
                    'reason' => 'Falha ao cadastrar o serviço no Conta Azul.',
                    'customer' => $booking->name,
                    'service' => $booking->service->name,
                    'date' => $booking->date
                ];
                continue;
            }

            if (!$serviceId) {
                $results[] = [
                    'success' => false,
                    'reason' => 'Falha ao cadastrar o cliente no Conta Azul.',
                    'customer' => $booking->name,
                    'service' => $booking->service->name,
                    'date' => $booking->date
                ];
                continue;
            }

            $resp = Http::withToken($token)->post("$this->caApiUrl/v1/sales", [
                'emission' => $now->format('Y-m-d\TH:i:s.v\Z'),
                'status' => 'COMMITTED',
                'customer_id' => $customerId,
                'services' => [[
                    'service_id' => $serviceId,
                    'description' => "{$booking->service->name} com {$booking->employee->firstname} em {$booking->date->format('d/m H:i')}",
                    'quantity' => 1,
                    'value' => $booking->service->price
                ]],
                'payment' => [
                    'type' => 'CASH',
                    'financialAccountId' => $bankId,
                    'installments' => [[
                        'number' => 1,
                        'value' => $booking->service->price,
                        'due_date' => $now->format('Y-m-d\T00:00:00.000\Z')
                    ]]
                ],
                'notes' => 'Integrado via Zingoo.'
            ]);

            $json = $resp->json();

            if ($resp->failed()) {
                Log::error('Falha ao integrar uma venda', ['json' => $json]);
                $msg = array_key_exists('message', $json) ?
                    $json['message'] : (array_key_exists('error_description', $json) ? $json['error_description'] : '');

                $results[] = [
                    'success' => false,
                    'reason' =>  $msg,
                    'customer' => $booking->name,
                    'employee' => $booking->employee->firstname . ' ' . $booking->employee->lastname,
                    'service' => $booking->service->name,
                    'date' => $booking->date,
                    'value' => $booking->service->price,
                    'resp' => $json
                ];
            } else {

                DB::update("update bookings set ca_sale_id = :saleId, ca_int_date = :now where id = :id", [
                    'saleId' => $json['id'],
                    'now' => $now,
                    'id' => $booking->id
                ]);

                $results[] = [
                    'success' => true,
                    'reason' =>  '',
                    'customer' => $booking->name,
                    'employee' => $booking->employee->firstname . ' ' . $booking->employee->lastname,
                    'service' => $booking->service->name,
                    'date' => $booking->date,
                    'value' => $booking->service->price,
                    'resp' => $json
                ];
            }
        }

        return view('conta-azul.sync-result', ['results' => $results]);
    }
}
