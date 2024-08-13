import Asset from "./Asset.js";
import UploadError from "../errors/UploadError.js";

export default class MediaLibrary {
    constructor(selector) {
        this.processing = false;
        this.currentAsset = null;
        this.wrapper = $(selector);

        // Preloading elements for JS performance
        this.elements = {
            form: this.wrapper.find('.media-library-form'),
            fileInput: this.wrapper.find('.media-library-file-input'),
            alertContainer: this.wrapper.find('.media-library-alert-container'),
            submitBtn: this.wrapper.find('.media-library-submit'),
            progress: this.wrapper.find('.media-library-progress'),
            progressBar: this.wrapper.find('.media-library-progress-bar'),
        };

        this.addEventListeners();
    }

    startProcessing() {
        this.elements.alertContainer.html('');
        this.processing = true;
        this.elements.fileInput.attr('disabled', 'disabled');
        this.elements.submitBtn.attr('data-state', 'processing');
        this.elements.progress.attr('data-state', 'processing');
        this.setProgress(0);
    }

    completeProcessing() {
        this.processing = false;
        this.currentAsset = null;
        this.elements.submitBtn.attr('disabled', false);
        this.elements.fileInput.attr('disabled', false);
        this.elements.form.get(0).reset();
        this.elements.submitBtn.attr('data-state', 'idle');
    }

    showAlert(cssClass, text) {
        this.elements.alertContainer.html('');
        $('<div class="alert py-1"></div>').addClass(cssClass).text(text).appendTo(this.elements.alertContainer);
    }

    addEventListeners() {
        this.elements.form.on('submit', () => this.startUpload());
        $(window).on('online', () => this.continueUpload());
    }

    setProgress(percentage) {
        const percentageString = percentage + '%';
        this.elements.progress.attr('data-progress', percentageString);
        this.elements.progressBar.css('width', percentageString);
    }

    continueUpload() {
        if (!this.processing || !this.currentAsset) {
            return;
        }

        this.elements.submitBtn.attr('data-state', 'processing');
        this.elements.progress.attr('data-state', 'processing');
        this.elements.alertContainer.html('');
        this.currentAsset.updateOffset().then(() => {
            this.processUpload();
        }).catch((e) => {
            this.handleError(e);
        });
    }

    startUpload() {
        if (this.processing) {
            return;
        }

        this.startProcessing();
        const files = this.elements.fileInput.get(0).files;

        if (files.length === 0) {
            this.showAlert('alert-danger', 'No file chosen');
            this.completeProcessing();
            this.elements.progress.removeAttr('data-state');
            return;
        }

        this.currentAsset = new Asset(files[0]);
        this.currentAsset.onProgressChange = (value) => this.setProgress(value);
        this.processUpload();
    }

    processUpload() {
        this.currentAsset.upload().then(() => {
            this.completeProcessing();
            this.showAlert('alert-success', 'Upload completed successfully!');
            this.elements.progress.attr('data-state', 'success');
        }).catch((e) => {
            this.handleError(e);
        });
    }

    handleError(e) {
        let message;

        if (e instanceof UploadError) {
            message = e.message;
            this.elements.submitBtn.attr('data-state', e.isNetworkError ? 'no-network' : 'failed');
        } else {
            this.elements.progress.attr('data-state', 'failed')
            message = 'Some error occurred.';
        }

        console.error(e);
        this.showAlert('alert-danger', message);
    }
}
