export default () => ({
    busy: false,
    attempted: false,
    progress: null,
    status: '',
    error: '',

    upload(form) {
        if (this.busy) return;

        const data = new FormData(form);
        this.busy = true;
        this.attempted = true;
        this.progress = null;
        this.status = 'Mengunggah file…';
        this.error = '';

        const request = new XMLHttpRequest();
        request.open('POST', form.action);
        request.setRequestHeader('Accept', 'application/json');
        request.responseType = 'json';

        request.upload.onprogress = (event) => {
            if (!event.lengthComputable || event.total === 0) return;

            this.progress = Math.floor((event.loaded / event.total) * 100);
            this.status = `Mengunggah file… ${this.progress}%`;
        };

        request.upload.onload = () => {
            this.progress = null;
            this.status = 'File terkirim. Data mahasiswa sedang diproses…';
        };

        request.onload = () => {
            if (request.status === 200 && request.response?.redirect) {
                this.progress = 100;
                this.status = 'Impor selesai. Memuat daftar mahasiswa…';
                window.location.assign(request.response.redirect);
                return;
            }

            const errors = {
                401: 'Silakan login kembali, lalu unggah ulang file.',
                403: 'Anda tidak memiliki akses untuk mengimpor data mahasiswa.',
                413: 'Ukuran file melebihi batas unggahan server.',
                419: 'Sesi kedaluwarsa. Muat ulang halaman sebelum mengunggah kembali.',
            };
            this.error = request.response?.errors?.template_file?.[0]
                ?? errors[request.status]
                ?? 'Impor gagal. Silakan coba lagi.';
            this.busy = false;
        };

        request.onerror = () => {
            this.error = 'Koneksi terputus. Periksa daftar mahasiswa sebelum mencoba lagi.';
            this.busy = false;
        };

        request.send(data);
    },
});
