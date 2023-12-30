import { redirectToTargetOrDefault } from '../utils.js'

class FetchController2 {
    constructor(endpoint) {
        this.endpoint = endpoint;
    }

    post(data) {
        return new Promise(async (resolve, reject) => {
            await fetch(this.endpoint, {
                method: 'POST',
                body: data
            })
                .then(async response => {
                    if (!response.ok) {
                        reject(await this.#handleError(response));
                    }
                    await response.json()
                        .then(data => resolve(data))
                        .catch(error => {
                            console.warn('Caught error:', error)
                            reject(new TextualError(response.status, 'Błąd podczas parsowania odpowiedzi JSON'))
                        })
                })
                .catch((error) => {
                    console.warn('Caught error:', error)
                    reject(new TextualError(error.status, 'Błąd sieciowy lub inny błąd'));
                });
        });
    }

    async #handleError(handledResponse) {
        const contentType = handledResponse.headers.get("Content-Type");
        if (contentType && contentType.includes("application/json")) {
            await handledResponse.json()
            .then(data => {
                return new JsonObjectError(handledResponse.status, data);
            })
            .catch((error) => {
                console.warn('Caught error:', error)
                return new TextualError(handledResponse.status, 'Błąd podczas parsowania błędu JSON');
            });
        } else {
            return new TextualError(handledResponse.status, this.#handleErrorStatusCode(handledResponse.status));
        }
    }

    #handleErrorStatusCode(statusCode) {
        switch (statusCode) {
            case 400:
                return 'Błąd w formularzu';
            case 401:
                return 'Nie jesteś zalogowany';
            case 409:
                return 'Nie masz uprawnień';
            case 404:
                return 'Strona nie istnieje';
            case 413:
                return 'Plik, który próbujesz przesłać ma zbyt duży rozmiar';
            default:
                return `Nieobsługiwany kod błędu: ${statusCode}`;
        }
    }
}

class TextualError extends Error {
    constructor(statusCode, message) {
        super(message);
        this.statusCode = statusCode;
    }
}

class JsonObjectError extends Error {
    constructor(statusCode, errorObject) {
        if (errorObject.error) {
            super(errorObject.error)
        } else {
            console.warn('JSON Headers but no error object')
            super('[JSONObjectError]')
        }
        this.statusCode = statusCode;
        this.errorObject = errorObject;
    }
}


export { FetchController2, TextualError, JsonObjectError };
