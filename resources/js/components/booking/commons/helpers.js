
export const sendToast = (title, message, type) => {
    $('#toast-header').text(title);
    $('#toast-body').text(message);
    $('#toast-booking').toast({delay: 15000});
    $('#toast-booking').toast('show');
}


export const sendNotification = (title, message, type, actions) => {
    if (actions)
        actions.setStatus(message)
    else
        sendToast(title, message, type);
}

export const handleResponseError = (e, actions) => {
    const errors = e.response.data.errors;
    if (errors) {
        for (const key in errors) {
            if (errors.hasOwnProperty(key)) {
                if(actions)
                    actions.setFieldError(key, errors[key]);
                else
                    console.log('Falha ao submeter', key, errors[key]);
            }
        }
    }
}

export const handleAxiosError = (e, actions = null) => {
    if (e.response) { // erros diferentes de 2xx
        if (e.response.data.errors) {
            return handleResponseError(e, actions)
        } else {
            switch (e.response.status) {
                case 400:
                    return sendNotification('Falha', 'Seu usuário não pode ser validado. Por favor, tente sair e entrar no sistema. Se o problema persistir, contacte o administrador', 'danger', actions);
                case 422:
                    return sendNotification('Falha', 'As informações enviadas não estavam corretas. Por favor, verifique e tente novamente.', 'danger', actions);
                case 401:
                    store.dispatch(logout());
                    return sendNotification('Falha', 'Usuário inválido. Por favor, tente sair e entrar no sistema. Se o problema persistir, contacte o administrador', 'danger', actions);
                case 403:
                    return sendNotification('Falha', 'Acesso não autorizado. Se você acha que isso está errado, contacte o administrador', 'danger', actions);
                default:
                    return sendNotification('Falha', `Erro inesperado (${e.response.data.message}). Tente novamente mais tarde. Se o problema persistir, contacte o administrador`, 'danger', actions);
            }
        }
    } else if (e.request) { // sem respota do servidor
        console.error(e.request);
        return sendNotification('Falha', `Sinto muito, mas o servidor não enviou nenhuma resposta`, 'danger', actions);
    } else { // falha ao fazer a requisição
        console.error(e);
        return sendNotification('Falha', `Sinto muito, houve uma falha ao fazer a requisição (${e.message}). Tente novamente mais tarde ou entre em contato.`, 'danger', actions);
    }
}