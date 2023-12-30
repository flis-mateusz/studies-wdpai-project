class FetchController {
    constructor(endpoint) {
        this.endpoint = endpoint;
    }

    /**
     * Makes an HTTP request to the given endpoint.
     * @param {string} method - The HTTP method to use for the request, e.g. 'GET', 'POST', etc.
     * @param {Object|FormData} [data] - The data to send with the request, e.g. for POST or PUT requests.
     * @returns {Promise} A Promise that resolves to the response data.
     */
    async #request(method, data = null) {
        try {
            const options = {
                method: method,
                headers: {}
            };

            if (data) {
                if (method === 'POST' || method === 'PUT') {
                    if (data instanceof FormData) {
                        options.body = data;
                    } else {
                        options.body = JSON.stringify(data);
                        options.headers['Content-Type'] = 'application/json';
                    }
                }
            }

            const response = await fetch(this.endpoint, options);

            if (!response.ok) {
                throw await this.#handleError(response);
            }

            try {
                return await response.json();
            } catch (error) {
                console.warn('Caught error:', error);
                throw new TextualError(response.status, 'Błąd podczas parsowania odpowiedzi JSON');
            }

        } catch (error) {
            if (error instanceof TextualError || error instanceof JsonObjectError) {
                throw error;
            } else {
                console.warn('Caught error:', error);
                throw new TextualError(error.status, 'Błąd sieciowy lub inny błąd');
            }
        }
    }

    async post(data) {
        return this.#request('POST', data);
    }

    async get() {
        return this.#request('GET');
    }

    /**
    * Handles errors returned by the server.
    *
    * @param {Response} handledResponse - The server response that contains the error.
    * @returns {Promise<TextualError|JsonObjectError>} A Promise that resolves to a TextualError or JsonObjectError, depending on the type of error returned by the server.
    */
    async #handleError(handledResponse) {
        const contentType = handledResponse.headers.get("Content-Type");
        if (contentType && contentType.includes("application/json")) {
            try {
                const responseData = await handledResponse.json();

                if (responseData.status === 401 && responseData.data.redirect_url) {
                    window.location.href = responseData.data.redirect_url;
                }
                return new JsonObjectError(handledResponse.status, responseData);
            } catch (error) {
                console.warn('Caught error:', error)
                return new TextualError(handledResponse.status, 'Błąd podczas parsowania błędu JSON');
            }
        } else {
            return new TextualError(handledResponse.status, this.#handleErrorStatusCode(handledResponse.status));
        }
    }

    #handleErrorStatusCode(statusCode) {
        switch (statusCode) {
            case 400:
                return 'Błąd w formularzu';
            case 401:
                window.location.href = '/login'
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
    constructor(statusCode, errorData) {
        if (errorData.error) {
            super(errorData.error)
        } else {
            console.warn('JSON Headers but no error object')
            super('[JSONObjectError]')
        }
        this.statusCode = statusCode;
        this.response = errorData;
    }
}


export { FetchController, TextualError, JsonObjectError };
