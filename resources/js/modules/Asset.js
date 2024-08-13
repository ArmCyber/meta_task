import UploadError from "../errors/UploadError.js";
import { AxiosError } from "axios";

export default class Asset {
    constructor(file) {
        this.chunkSize = 1000000; // 1MB
        this.assetId = null; // placeholder
        this.onProgressChange = null;

        if (!file instanceof File) {
            throw new UploadError('Invalid file provided.');
        }

        this.file = file;
        this.totalSize = this.file.size;
        this.offset = 0;
    }

    async updateOffset() {
        if (!this.assetId) {
            return;
        }

        const response = await this.sendRequest('get',`/media/${this.assetId}/offset`)

        this.offset = response.data.offset;
    }

    async upload() {
        while (this.offset < this.totalSize) {
            const chunk = this.file.slice(this.offset, this.offset + this.chunkSize);
            const formData = await this.buildFormData(chunk);
            const response = await this.sendRequest('post','/media/upload', formData);

            if (this.offset === 0) {
                this.assetId = response.data.id;
            }

            this.offset += this.chunkSize;
            const progress = Math.min(Math.floor(100 * this.offset / this.totalSize), 100);
            this.onProgressChange?.(progress);
        }
    }

    async sendRequest(method, url, data = undefined) {
        try {
            return await axios[method](url, data);
        } catch (e) {
            if (e instanceof AxiosError) {
                if (e.code === 'ERR_NETWORK') {
                    throw new UploadError('No network connection, please reconnect to the internet.', true);
                } else if (e.response?.status === 422) {
                    const message = e.response.data?.message ?? 'Invalid request';
                    throw new UploadError(message);
                }

                throw e;
            }
        }
    }

    async buildFormData(chunk) {
        const formData = new FormData();
        formData.append('chunk', chunk);

        if (this.offset === 0) {
            formData.append('init', '1');
            formData.append('name', this.file.name);
            formData.append('total_size', this.totalSize)
        } else {
            if (!this.assetId) {
                throw new UploadError('Upload failed.');
            }

            formData.append('id', this.assetId);
            formData.append('offset', this.offset.toString());
        }

        return formData;
    }
}
