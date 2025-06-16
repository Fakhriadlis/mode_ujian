/**
 * Modul JavaScript untuk mengelola tampilan aktivitas pada halaman kursus Moodle berdasarkan Mode Ujian.
 * Menyembunyikan aktivitas non-ujian saat mode ujian aktif, kecuali untuk pengguna dengan hak khusus (capability).
 *
 * @module local_mode_ujian/toggle
 */

define(['jquery'], function ($) {
    return {
        /**
         * Entry point utama untuk menginisialisasi logika Mode Ujian.
         *
         * @param {number|string} courseid - ID kursus Moodle.
         * @param {string} status - Status awal mode ujian ('0' untuk non-ujian, '1' untuk mode ujian).
         * @param {boolean} hascap - Apakah pengguna memiliki kemampuan khusus (misalnya: admin/dosen).
         */
        init: function (courseid, status, hascap) {
            const isAllowed = hascap;

            /**
             * Menerapkan logika penyembunyian/tampilan aktivitas berdasarkan status mode ujian.
             *
             * @param {string} status - Status mode ujian ('0' = non-ujian, '1' = mode ujian).
             */
            function applyModeUjian(status) {
                $('.course-content .activity').each(function () {
                    const $activity = $(this);
                    const modtype = $activity.attr('class');
                    const isQuiz = modtype.includes('modtype_quiz');
                    const instanceName = $activity.find('.instancename').text().trim().toUpperCase();
                    const isExam = isQuiz && ['UTS', 'UAS', 'EXAM', 'UJIAN'].some(keyword => instanceName.includes(keyword));

                    if (status == '1') {
                        if (isQuiz) {
                            if (isExam || isAllowed) {
                                $activity.show();
                            } else {
                                $activity.hide();
                            }
                        } else {
                            if (isAllowed) {
                                $activity.show();
                            } else {
                                $activity.hide();
                            }
                        }
                    } else {
                        $activity.show();
                    }
                });
            }

            /**
             * Memastikan applyModeUjian dijalankan berulang beberapa kali untuk mengantisipasi konten dinamis.
             *
             * @param {string} status - Status mode ujian yang diterapkan.
             * @param {number} [attempts=0] - Jumlah percobaan pemanggilan (maksimal 10 kali).
             */
            function ensureApply(status, attempts = 0) {
                applyModeUjian(status);
                if (attempts < 10) {
                    setTimeout(() => ensureApply(status, attempts + 1), 300);
                }
            }

            ensureApply(status);

            const toggleWrapper = document.getElementById('mode-ujian-toggle');
            if (toggleWrapper) {
                const navTabs = document.querySelector('.secondary-navigation') || document.querySelector('.nav-tabs');
                if (navTabs) {
                    const wrapper = document.createElement('div');
                    wrapper.style.display = 'flex';
                    wrapper.style.justifyContent = 'flex-end';
                    wrapper.style.margin = '2px 6px';
                    wrapper.appendChild(toggleWrapper);
                    navTabs.parentNode.insertBefore(wrapper, navTabs.nextSibling);
                }
            }

            const observerTarget = document.querySelector('.course-content');
            if (observerTarget) {
                const observer = new MutationObserver(() => {
                    const current = document.querySelector('input[name="exam_mode"]:checked');
                    if (current) {
                        applyModeUjian(current.value);
                    }
                });
                observer.observe(observerTarget, { childList: true, subtree: true });
            }

          
            document.querySelectorAll('input[name="exam_mode"]').forEach(input => {
                input.addEventListener('change', function () {
                    const newstatus = this.value;
                    fetch(M.cfg.wwwroot + '/local/mode_ujian/save_mode_ujian.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'courseid=' + courseid + '&status=' + newstatus
                    })
                        .then(() => {
                            location.reload(true); 
                        })
                        .catch(() => {
                            location.reload(true); 
                        });
                });
            });

        }
    };
});