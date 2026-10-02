<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Pastikan role user sesuai dengan role yang dibutuhkan rute.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $roleUser = session()->get('role');

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah role user ada di dalam daftar argument yang diizinkan
        if (!empty($arguments) && !in_array($roleUser, $arguments, true)) {
            // Arahkan kembali ke dashboard sesuai role yang sah
            $dashboard = ($roleUser === 'guru') ? '/guru' : '/mahasiswa';
            return redirect()->to($dashboard)->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}
