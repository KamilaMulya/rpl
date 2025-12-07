<?php
include("koneksi.php");

function checkLogin($data, &$errors)
{
    global $koneksi;

    // Cek apakah session sudah aktif
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $email = htmlspecialchars(trim($data["email"]));
    $password = $data["password"];

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    }

    if (count($errors) === 0) {
        // Ambil data user berdasarkan email
        $result = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password
            if ($password == $user["password"]) {
                $_SESSION["user"] = $user;

                // Set role session
                if ($user["role"] == '3') {
                    $_SESSION["role"] = "Admin";
                } elseif ($user["role"] == '2') {
                    $_SESSION["role"] = "Pengawas";
                } else {
                    $_SESSION["role"] = "Guru";
                }

                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Password salah.";
            }
        } else {
            $errors[] = "Email tidak ditemukan.";
        }
    }
}


function buatakun($data, &$errors) {
    global $koneksi;

    $nama_user = htmlspecialchars(trim($data['nama_user']));
    $email = htmlspecialchars(trim($data['email']));
    $no_hp = htmlspecialchars(trim($data['no_hp']));
    $password = htmlspecialchars(trim($data['password']));
    $role = htmlspecialchars(trim($data['role']));

    if (empty($nama_user)) $errors[] = "Nama tidak boleh kosong.";
    if (empty($email)) $errors[] = "Email tidak boleh kosong.";
    if (empty($no_hp)) $errors[] = "No HP tidak boleh kosong.";
    if (empty($password)) $errors[] = "Password tidak boleh kosong.";
    if (empty($role)) $errors[] = "Role harus dipilih.";

    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $errors[] = "Email sudah terdaftar.";
    }

    if (count($errors) == 0) {
        $query = "INSERT INTO user (nama_user, email, password, role, no_hp) VALUES ('$nama_user', '$email', '$password', '$role', '$no_hp')";

        if (mysqli_query($koneksi, $query)) {
            header("Location: login.php?pesan=daftar_sukses");
            exit;
        } else {
            $errors[] = "Gagal membuat akun: " . mysqli_error($koneksi);
        }
    }
}
