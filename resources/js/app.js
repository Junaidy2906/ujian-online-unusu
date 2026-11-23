import './bootstrap';

import Alpine from 'alpinejs';
import studentUpload from './student-upload';

window.Alpine = Alpine;

Alpine.data('studentUpload', studentUpload);

Alpine.start();
