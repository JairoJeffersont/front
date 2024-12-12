async function requestApi(url, method, data, isMultipart = false) {
    showModal()
    const headers = {};

    const token = localStorage.getItem('usuario_token');
    if (token) {
        headers['Authorization-Info'] = `Bearer ${token}`;
    }

    if (!isMultipart) {
        headers['Content-Type'] = 'application/json';
    }

    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: method,
            headers: headers,
            processData: !isMultipart,
            contentType: isMultipart ? false : 'application/json',
            data: isMultipart ? data : JSON.stringify(data),
            success: function (response) {
                resolve(response);
            },
            error: function (xhr, status, error) {
                if (xhr.statusText == 'Forbidden' || xhr.statusText == 'Not Found') {
                    window.location.href = '?secao=login';
                } else {
                    reject({ xhr, status, error });
                }
            }
        });
        hideModal()
    });
}
