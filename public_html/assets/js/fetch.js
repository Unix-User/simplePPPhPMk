function sync(url, body, device) {
    fetch(url, {
        method: "POST",
        // whatever data you want to post with a key-value pair
        body: body,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    }).then(function (response) {
        response.text()
            .then(function (result) {
                const element = document.querySelector('#post-request');
                const myArray = result.split("|");
                alert('Iniciando a sincronização');
                myArray.forEach(function (array) {
                    if (array !== '') {
                        console.log(array)
                        const requestOptions = {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: array
                        };
                        (async () => {
                            const response = await fetch('/device/'+device+'/sync', requestOptions);
                            const data = await response.text();
                            element.innerHTML = data;
                        })();
                    }

                });
            })
    }).catch(function (err) {
        alert('Falha ao receber informações, verifique se as permissões de CORS no servidor do site ' + url + 'estão habilitadas');
    });
}