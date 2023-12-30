class FetchController {
    constructor(endpoint) {
        this.endpoint = endpoint;
    }

    async post(data) {
        try {
            const response = await fetch(this.endpoint, {
                method: 'POST',
                body: data
            });
            return await (response.ok ? response.json() : Promise.reject({ response, status: response.status }));
        } catch (error) {
            throw await this.#handleError(error);
        }
    }

    async get() {
        try {
            const response = await fetch(this.endpoint);
            return await (response.ok ? response.json() : Promise.reject({ response, status: response.status }));
        } catch (error) {
            throw await this.#handleError(error);
        }
    }

    async #handleError(error) {
        if (!error.response) {
            return TextualError(error.status, 'Błąd sieciowy lub inny błąd');
        }

        const contentType = error.response.headers.get("Content-Type");
        if (contentType && contentType.includes("application/json")) {
            try {
                const data = await error.response.json();
                return new JsonObjectError(error.status, data);
            } catch (jsonError) {
                return 'Błąd podczas parsowania odpowiedzi JSON';
            }
        } else {
            return TextualError(error.status, this.#handleErrorStatusCode(error.status));
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
        super(JSON.stringify(errorObject));
        this.statusCode = statusCode;
        this.errorObject = errorObject;
        
    }
}


export { FetchController, TextualError, JsonObjectError };
