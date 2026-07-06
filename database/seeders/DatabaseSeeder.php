<?php

namespace Database\Seeders;

use App\Domain\Entity\Artikel;
use App\Domain\Entity\BalasanLaporan;
use App\Domain\Entity\Guru;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Kelas;
use App\Domain\Entity\LaporanKegiatan;
use App\Domain\Entity\Murid;
use App\Domain\Entity\OrangTua;
use App\Domain\Entity\Permission;
use App\Domain\Entity\Presensi;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── ROLES ─────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['nama_role' => 'admin'],     ['is_active' => true]);
        $guruRole  = Role::firstOrCreate(['nama_role' => 'guru'],      ['is_active' => true]);
        $ortRole   = Role::firstOrCreate(['nama_role' => 'orang_tua'], ['is_active' => true]);

        // ─── PERMISSIONS ───────────────────────────────────────
        foreach ([
            'artikel.create', 'artikel.edit', 'artikel.delete', 'artikel.view',
            'kelas.create', 'kelas.delete', 'kelas.view',
            'presensi.create', 'presensi.edit', 'presensi.view',
            'laporan.create', 'laporan.view', 'laporan.delete', 'laporan.reply',
            'murid.create', 'murid.edit', 'murid.view',
            'user.manage', 'role.manage',
        ] as $p) {
            Permission::firstOrCreate(['nama_permission' => $p]);
        }

        $adminRole->permissions()->sync(Permission::pluck('id_permission')->toArray());

        // ─── KELAS ─────────────────────────────────────────────
        $kelasA = Kelas::firstOrCreate(['nama_kelas' => 'Kuncup Anyelir'], ['deskripsi' => 'Kelas usia 3-4 tahun']);
        $kelasB = Kelas::firstOrCreate(['nama_kelas' => 'Kuncup Mawar'],   ['deskripsi' => 'Kelas usia 4-5 tahun']);
        $kelasC = Kelas::firstOrCreate(['nama_kelas' => 'Kuncup Melati'],  ['deskripsi' => 'Kelas usia 5-6 tahun']);

        // ─── ADMIN ─────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@aluna.id'], [
            'nama' => 'Administrator', 'password' => Hash::make('password'),
            'no_hp' => '081234567890', 'jenis_kelamin' => 'laki-laki', 'status' => 'aktif',
        ]);
        $admin->roles()->syncWithoutDetaching([$adminRole->id_role => ['is_active' => true]]);

        // ─── GURU (3 guru) ─────────────────────────────────────
        $guruData = [
            ['nama' => 'Budi Santoso',    'email' => 'guru@aluna.id',      'no_hp' => '082345678901', 'spesialisasi' => 'Seni & Kreativitas'],
            ['nama' => 'Dewi Lestari',    'email' => 'dewi@aluna.id',      'no_hp' => '082456789012', 'spesialisasi' => 'Bahasa & Literasi'],
            ['nama' => 'Eko Prasetyo',    'email' => 'eko@aluna.id',       'no_hp' => '082567890123', 'spesialisasi' => 'Motorik & Olahraga'],
        ];
        $guruEntities = [];
        foreach ($guruData as $gd) {
            $u = User::firstOrCreate(['email' => $gd['email']], [
                'nama' => $gd['nama'], 'password' => Hash::make('password'),
                'no_hp' => $gd['no_hp'], 'status' => 'aktif',
            ]);
            $u->roles()->syncWithoutDetaching([$guruRole->id_role => ['is_active' => true]]);
            $guruEntities[] = Guru::firstOrCreate(['id_user' => $u->id_user], ['spesialisasi' => $gd['spesialisasi']]);
        }

        // ─── ORANG TUA & MURID ─────────────────────────────────
        $orangTuaData = [
            ['nama' => 'Siti Rahayu',    'email' => 'orangtua@aluna.id',  'pekerjaan' => 'Wiraswasta'],
            ['nama' => 'Agus Wirawan',   'email' => 'agus@aluna.id',      'pekerjaan' => 'Pegawai Swasta'],
            ['nama' => 'Rina Handayani', 'email' => 'rina@aluna.id',      'pekerjaan' => 'Guru SD'],
            ['nama' => 'Hendra Susanto', 'email' => 'hendra@aluna.id',    'pekerjaan' => 'Dokter'],
            ['nama' => 'Fitri Amalia',   'email' => 'fitri@aluna.id',     'pekerjaan' => 'Ibu Rumah Tangga'],
        ];
        $muridData = [
            // [nama, jenis_kelamin, tanggal_lahir, kelas]
            ['Zahra Rahayu',      'perempuan', '2021-03-15', $kelasA],
            ['Rafif Wirawan',     'laki-laki', '2020-07-22', $kelasB],
            ['Nadia Handayani',   'perempuan', '2019-11-05', $kelasC],
            ['Rizky Susanto',     'laki-laki', '2021-01-18', $kelasA],
            ['Aulia Amalia',      'perempuan', '2020-09-30', $kelasB],
            ['Farhan Rahayu',     'laki-laki', '2019-05-12', $kelasC],
            ['Keisha Wirawan',    'perempuan', '2021-06-08', $kelasA],
            ['Dimas Handayani',   'laki-laki', '2020-04-25', $kelasB],
            ['Salma Susanto',     'perempuan', '2019-08-14', $kelasC],
            ['Ghifari Amalia',    'laki-laki', '2021-02-03', $kelasA],
        ];

        $ortEntities = [];
        foreach ($orangTuaData as $i => $od) {
            $u = User::firstOrCreate(['email' => $od['email']], [
                'nama' => $od['nama'], 'password' => Hash::make('password'),
                'no_hp' => '08' . rand(100000000, 999999999), 'status' => 'aktif',
            ]);
            $u->roles()->syncWithoutDetaching([$ortRole->id_role => ['is_active' => true]]);
            $ortEntities[] = OrangTua::firstOrCreate(['id_user' => $u->id_user], ['pekerjaan' => $od['pekerjaan']]);
        }

        $muridEntities = [];
        foreach ($muridData as $i => $md) {
            $ort = $ortEntities[$i % count($ortEntities)];
            $muridEntities[] = Murid::firstOrCreate(
                ['nama_murid' => $md[0], 'id_orang_tua' => $ort->id_orang_tua],
                [
                    'id_kelas'      => $md[3]->id_kelas,
                    'jenis_kelamin' => $md[1],
                    'tanggal_lahir' => $md[2],
                    'status_murid'  => 'aktif',
                ]
            );
        }

        // ─── JADWAL KELAS ──────────────────────────────────────
        $jadwalData = [
            [$kelasA, $guruEntities[0], '2025-04-07', '07:30', '09:30', 'Mengenal Warna & Bentuk'],
            [$kelasA, $guruEntities[0], '2025-04-09', '07:30', '09:30', 'Seni Menggambar Bebas'],
            [$kelasA, $guruEntities[0], '2025-04-14', '07:30', '09:30', 'Bermain Pasir Kinetik'],
            [$kelasB, $guruEntities[1], '2025-04-07', '09:30', '11:30', 'Pengenalan Huruf A-E'],
            [$kelasB, $guruEntities[1], '2025-04-10', '09:30', '11:30', 'Membaca Buku Gambar'],
            [$kelasB, $guruEntities[1], '2025-04-15', '09:30', '11:30', 'Bercerita & Ekspresi'],
            [$kelasC, $guruEntities[2], '2025-04-08', '10:00', '12:00', 'Senam Pagi & Motorik'],
            [$kelasC, $guruEntities[2], '2025-04-11', '10:00', '12:00', 'Permainan Outdoor'],
            [$kelasC, $guruEntities[2], '2025-04-16', '10:00', '12:00', 'Yoga Anak'],
        ];

        $jadwalEntities = [];
        foreach ($jadwalData as $jd) {
            $jadwalEntities[] = JadwalKelas::firstOrCreate(
                ['id_kelas' => $jd[0]->id_kelas, 'tanggal' => $jd[2], 'topik' => $jd[5]],
                [
                    'id_guru'     => $jd[1]->id_guru,
                    'jam_mulai'   => $jd[3],
                    'jam_selesai' => $jd[4],
                ]
            );
        }

        // ─── PRESENSI ──────────────────────────────────────────
        $statusList = ['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'tidak_hadir'];
        foreach ($jadwalEntities as $jadwal) {
            $muridDiKelas = array_filter($muridEntities, fn($m) => $m->id_kelas === $jadwal->id_kelas);
            foreach ($muridDiKelas as $murid) {
                $status = $statusList[array_rand($statusList)];
                Presensi::updateOrCreate(
                    ['id_jadwal' => $jadwal->id_jadwal, 'id_murid' => $murid->id_murid],
                    [
                        'status_kehadiran' => $status,
                        'keterangan'       => $status === 'sakit' ? 'Demam' : ($status === 'izin' ? 'Acara keluarga' : null),
                        'dicatat_oleh'     => $admin->id_user,
                    ]
                );
            }
        }

        // ─── ARTIKEL ───────────────────────────────────────────
        $artikelData = [
            ['Penerimaan Murid Baru Tahun Ajaran 2025/2026',
             'TK Aluna Montessori membuka pendaftaran murid baru untuk tahun ajaran 2025/2026. Kuota terbatas hanya 60 murid per angkatan. Daftarkan putra-putri Anda segera dan dapatkan pengalaman belajar terbaik dengan metode Montessori.',
             $guruEntities[1]->id_user, 'published'],
            ['Metode Montessori: Belajar Melalui Bermain',
             'Metode Montessori yang kami terapkan berfokus pada kemandirian anak dalam belajar. Anak diberikan kebebasan untuk memilih aktivitas yang mereka minati, sehingga proses belajar menjadi lebih menyenangkan dan efektif.',
             $admin->id_user, 'published'],
            ['Kegiatan Hari Kartini di Aluna Montessori',
             'Dalam rangka memperingati Hari Kartini, TK Aluna Montessori mengadakan berbagai kegiatan menarik seperti fashion show busana daerah, lomba memasak, dan pertunjukan seni budaya dari seluruh nusantara.',
             $guruEntities[0]->id_user, 'published'],
            ['Pentingnya Stimulasi Motorik Halus pada Anak Usia Dini',
             'Motorik halus adalah kemampuan anak menggunakan otot-otot kecil, terutama tangan dan jari. Di Aluna Montessori, kami menyediakan berbagai aktivitas seperti melipat kertas, meronce, dan bermain plastisin.',
             $guruEntities[2]->id_user, 'published'],
            ['Jadwal Libur Sekolah Semester Genap 2025',
             'Berikut adalah jadwal libur resmi TK Aluna Montessori untuk semester genap tahun 2025. Mohon orang tua dapat menyesuaikan jadwal kegiatan keluarga dengan kalender akademik sekolah.',
             $admin->id_user, 'published'],
            ['Program Parenting: Mendampingi Tumbuh Kembang Anak',
             'TK Aluna Montessori mengundang seluruh orang tua murid untuk mengikuti program parenting yang akan dilaksanakan setiap bulan. Program ini membahas berbagai topik seputar tumbuh kembang anak usia dini.',
             $guruEntities[1]->id_user, 'draft'],
        ];

        foreach ($artikelData as $ad) {
            Artikel::firstOrCreate(
                ['judul_artikel' => $ad[0]],
                [
                    'konten_artikel'  => $ad[1],
                    'id_user'         => $ad[2],
                    'status_artikel'  => $ad[3],
                    'tanggal_publish' => now()->subDays(rand(1, 30)),
                ]
            );
        }

        // ─── LAPORAN KEGIATAN ──────────────────────────────────
        $laporanData = [
            ['Perkembangan Motorik Zahra Bulan April',
             'Zahra menunjukkan perkembangan motorik halus yang sangat baik. Ia mampu mewarnai gambar dengan rapi dan menyelesaikan puzzle 20 keping dengan mandiri. Semangat belajarnya tinggi dan selalu antusias mengikuti kegiatan.',
             $muridEntities[0], $jadwalEntities[0], $guruEntities[0]],
            ['Laporan Kegiatan Rafif - Pengenalan Huruf',
             'Rafif sudah mulai mengenal 10 huruf alfabet dan mampu menyebutkannya dengan benar. Ia cukup aktif dalam kegiatan membaca buku bergambar. Perlu latihan lebih untuk konsentrasi yang lebih panjang.',
             $muridEntities[1], $jadwalEntities[3], $guruEntities[1]],
            ['Catatan Perkembangan Nadia - Senam Pagi',
             'Nadia sangat menyukai kegiatan senam pagi. Gerakan tubuhnya koordinatif dan ia mampu mengikuti instruksi dengan baik. Nadia juga menunjukkan jiwa kepemimpinan dengan memimpin teman-temannya melakukan pemanasan.',
             $muridEntities[2], $jadwalEntities[6], $guruEntities[2]],
            ['Laporan Seni - Rizky Menggambar Bebas',
             'Rizky mengekspresikan kreativitasnya melalui gambar yang penuh warna. Ia membuat gambar rumah dan keluarganya dengan detail yang mengesankan untuk anak seusianya. Imajinasi Rizky sangat kaya.',
             $muridEntities[3], $jadwalEntities[1], $guruEntities[0]],
            ['Perkembangan Bahasa Aulia - Bercerita',
             'Aulia menunjukkan kemampuan bercerita yang luar biasa. Ia mampu menceritakan kembali isi buku dengan urutan yang benar dan menggunakan kosakata yang beragam. Percaya diri berbicara di depan teman-temannya.',
             $muridEntities[4], $jadwalEntities[5], $guruEntities[1]],
            ['Laporan Motorik Farhan - Permainan Outdoor',
             'Farhan sangat aktif dalam kegiatan outdoor. Ia menunjukkan keberanian dalam melakukan tantangan fisik seperti memanjat dan melompat. Kemampuan keseimbangan dan koordinasi tubuhnya sangat baik.',
             $muridEntities[5], $jadwalEntities[7], $guruEntities[2]],
        ];

        $laporanEntities = [];
        foreach ($laporanData as $ld) {
            $laporanEntities[] = LaporanKegiatan::firstOrCreate(
                ['judul_laporan' => $ld[0], 'id_murid' => $ld[2]->id_murid],
                [
                    'isi_laporan' => $ld[1],
                    'id_jadwal'   => $ld[3]->id_jadwal,
                    'id_guru'     => $ld[4]->id_guru,
                ]
            );
        }

        // ─── BALASAN LAPORAN ───────────────────────────────────
        $balasanData = [
            [$laporanEntities[0], $ortEntities[0]->id_user, 'Terima kasih Bu Guru atas laporannya 🙏 Kami sangat senang mendengar perkembangan Zahra. Di rumah juga ia suka mewarnai.'],
            [$laporanEntities[0], $guruEntities[1]->id_user, 'Sama-sama Bu Siti. Zahra memang anak yang sangat telaten dan fokus. Terus dukung ya di rumah!'],
            [$laporanEntities[1], $ortEntities[1]->id_user, 'Alhamdulillah Pak, terima kasih infonya. Rafif memang masih perlu banyak latihan konsentrasi. Kami akan bantu di rumah.'],
            [$laporanEntities[2], $ortEntities[2]->id_user, 'Wah senang sekali mendengar Nadia aktif! Dia memang suka sekali olahraga. Terima kasih Bu Guru sudah memperhatikan perkembangannya.'],
            [$laporanEntities[3], $ortEntities[3]->id_user, 'MasyaAllah, Rizky gambar keluarga ya? Di rumah juga sering menggambar. Terima kasih laporannya Bu Guru!'],
            [$laporanEntities[4], $ortEntities[4]->id_user, 'Terima kasih Bu Dewi! Aulia memang suka sekali bercerita di rumah. Senang bisa lihat perkembangannya di sekolah juga.'],
        ];

        foreach ($balasanData as $bd) {
            BalasanLaporan::firstOrCreate(
                ['id_laporan' => $bd[0]->id_laporan, 'id_user' => $bd[1]],
                ['isi_balasan' => $bd[2]]
            );
        }

        // ─── DONE ──────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Seeder selesai! Data yang dibuat:');
        $this->command->info('   • 3 Kelas (Kuncup Anyelir, Mawar, Melati)');
        $this->command->info('   • 3 Guru + 5 Orang Tua + 10 Murid');
        $this->command->info('   • 9 Jadwal Kelas');
        $this->command->info('   • Presensi untuk semua jadwal');
        $this->command->info('   • 6 Artikel');
        $this->command->info('   • 6 Laporan Kegiatan + Balasan');
        $this->command->info('');
        $this->command->info('📧 Akun login:');
        $this->command->info('   Admin    : admin@aluna.id / password');
        $this->command->info('   Guru     : guru@aluna.id / password');
        $this->command->info('   Orang Tua: orangtua@aluna.id / password');
    }
}
