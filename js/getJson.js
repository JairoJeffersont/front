async function requestApi(url, method, data) {
    const headers = {
        'Content-Type': 'application/json',
    };

    //if (token) {
        headers['Authorization-Info'] = `Bearer ${localStorage.getItem('usuario_token')}`;
    //}

    const response = await axios({
        url: url,
        method: method,
        headers: headers,
        data: data || null
    });

    return response
}
