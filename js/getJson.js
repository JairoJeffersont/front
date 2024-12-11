async function requestApi(url, method, data, token) {
    const headers = {
        'Content-Type': 'application/json',
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(url, {
        method: method,
        headers: headers,
        body: data ? JSON.stringify(data) : null,  
    });

    

    if (response.status === 200) {
        const responseData = await response.json();
        return { status: response.status, data: responseData };
    }


    if(response.statusText == 'Not Found'){
        showAlert('danger', 'api of', 0);
        hideModal()
    }


    const errorData = await response.json();

    throw { status: response.status, message: response.statusText, error: errorData };
}
