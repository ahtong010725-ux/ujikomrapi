<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auth | I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

<div class="auth-wrapper">

    <div class="auth-container">

        <!-- IMAGE SIDE -->
        <div class="auth-image">
            <img src="/images/login-bg.jpg">
            <div class="overlay">
                <h2>Welcome to I FOUND</h2>
                <p>Helping students reconnect with lost and found items quickly & safely.</p>
            </div>
        </div>

        <!-- FORM SIDE -->
        <div class="auth-form">

            <!-- LOGIN FORM -->
            <form class="form login-form active">
                <h2>Login</h2>

                <div class="input-group">
                    <input type="text" required>
                    <label>NISN</label>
                </div>

                <div class="input-group">
                    <input type="password" id="loginPass" required>
                    <label>Password</label>
                    <span class="eye" onclick="togglePassword('loginPass')">👁</span>
                </div>

                <button type="submit" class="btn">Sign In</button>

                <p class="switch">Belum punya akun?
                    <span onclick="switchForm()">Sign Up</span>
                </p>
            </form>

            <!-- REGISTER FORM -->
            <form class="form register-form">
                <h2>Register</h2>

                <div class="grid-2">

                    <div class="input-group">
                        <input type="text" required>
                        <label>NISN</label>
                    </div>

                    <div class="input-group">
                        <input type="text" required>
                        <label>Nama</label>
                    </div>

                    <div class="input-group">
                        <input type="text" required>
                        <label>Kelas</label>
                    </div>

                    <div class="input-group">
                        <input type="date" required>
                        <label class="active-label">Tanggal Lahir</label>
                    </div>

                    <div class="input-group">
                        <select required>
                            <option value=""></option>
                            <option>Laki-laki</option>
                            <option>Perempuan</option>
                        </select>
                        <label class="active-label">Jenis Kelamin</label>
                    </div>

                    <div class="input-group">
                        <input type="text" required>
                        <label>Nomor Telepon</label>
                    </div>

                    <div class="input-group full">
                        <input type="file" required>
                        <label class="active-label">Upload Foto</label>
                    </div>

                    <div class="input-group full">
                        <input type="password" id="regPass" required>
                        <label>Password</label>
                        <span class="eye" onclick="togglePassword('regPass')">👁</span>
                    </div>

                </div>

                <button type="submit" class="btn">Register</button>

                <p class="switch">Sudah punya akun?
                    <span onclick="switchForm()">Sign In</span>
                </p>
            </form>

        </div>
    </div>

</div>

<script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
