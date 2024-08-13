export default class UploadError extends Error {
    constructor(message, isNetworkError = false) {
        super(message);
        this.isNetworkError = isNetworkError;
    }
}
