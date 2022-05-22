class ApiFetch {

    /**
     * Constructor.
     * @param endpointUrl The URL for the internal API endpoint (this is not
     *        for SHortcut's URL)
     */
    constructor (endpointUrl) {
        this.url = endpointUrl;
    }

    /**
     * Set a callback for the `catch` result of the `fetch` api used to make
     * requests.
     * @param callback a function fn(error) to process errors when the API call
     *        fail.
     */
    setErrorHandler(callback) {
        this.errorHandlerCalback = callback;
    }

    /**
     * Send a GET request to the endpoint URL
     * @param params JSON object containing a list of parameters to send in the
     *        request. They will be converted into URL parameters.
     * @returns {Promise<Response | *>} Returns the call to `fetch` api already
     *          pointing `catch` to call whatever was passed via
     *          `setErrorHandler`
     */
    get(params) {
        const paramsForGet = new URLSearchParams(params);
        return fetch(this.url + "?" + paramsForGet)
            .catch( error => this.errorHandlerCalback(error) );
    }

    /**
     * Send a GET request to the endpoint and promises a text.
     * @param params
     * @returns {Promise<string>}
     */
    getText(params) {
        return this.get(params).
            then(data => data.text());
    }

}