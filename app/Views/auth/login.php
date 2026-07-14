<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Masuk - Al-Miftah</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="<?= base_url('assets/js/db.js') ?>"></script>
    <style>
        .login-card {
            background: #FFFFFF;
            border: 1px solid #F1F5F9;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .bg-pattern {
            background-color: #F4F7F6;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(4, 108, 78, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(212, 160, 23, 0.04) 0%, transparent 60%);
            position: relative;
        }
        .bg-pattern::before {
            content: '';
            position: fixed;
            inset: 0;
            opacity: 0.03;
            background-image:
                repeating-linear-gradient(45deg, #046C4E 0px, #046C4E 1px, transparent 1px, transparent 20px),
                repeating-linear-gradient(-45deg, #D4A017 0px, #D4A017 1px, transparent 1px, transparent 20px);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-pattern font-sans text-on-surface min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[380px]">

        <!-- Brand -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-3 shadow-lg shadow-primary/25 ring-4 ring-primary/10">
                <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings: 'FILL' 1;">mosque</span>
            </div>
            <h1 class="text-xl font-heading font-bold text-primary">Al-Miftah</h1>
            <p class="text-xs text-on-surface-variant mt-1.5 leading-relaxed max-w-xs mx-auto">
                Sistem Informasi Manajemen Program Al-Miftah Lil Ulum
            </p>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl p-6">

            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-3 bg-error-container/40 border-l-4 border-error rounded-lg flex items-start gap-2 text-sm text-on-surface">
                <span class="material-symbols-outlined text-error text-base shrink-0 mt-0.5">error_outline</span>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')): ?>
            <div class="mb-4 p-3 bg-primary-container/20 border-l-4 border-primary rounded-lg flex items-start gap-2 text-sm text-on-surface">
                <span class="material-symbols-outlined text-primary text-base shrink-0 mt-0.5">check_circle</span>
                <span><?= session()->getFlashdata('message') ?></span>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login') ?>" method="post" class="space-y-4">

                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-base transition-colors group-focus-within:text-primary">person</span>
                    <input type="email" name="email" id="email" required autocomplete="username"
                           value="<?= esc(old('email')) ?>"
                           placeholder="Email"
                           class="w-full pl-10 pr-4 py-3 bg-surface/60 border border-outline-variant/60 rounded-xl text-sm
                                  transition-all duration-200
                                  focus:bg-white focus:border-primary/40 focus:ring-2 focus:ring-primary/10
                                  outline-none placeholder:text-on-surface-variant/40"/>
                </div>

                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-base transition-colors group-focus-within:text-primary">lock</span>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           placeholder="Kata Sandi"
                           class="w-full pl-10 pr-10 py-3 bg-surface/60 border border-outline-variant/60 rounded-xl text-sm
                                  transition-all duration-200
                                  focus:bg-white focus:border-primary/40 focus:ring-2 focus:ring-primary/10
                                  outline-none placeholder:text-on-surface-variant/40"/>
                    <button type="button" onclick="togglePassword()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface-variant transition-colors p-0.5">
                        <span class="material-symbols-outlined text-base" id="eyeIcon">visibility_off</span>
                    </button>
                </div>

                <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" id="remember" value="1"
                           class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30 transition-all"/>
                    <span class="text-xs text-on-surface-variant group-hover:text-primary transition-colors">Ingat Saya</span>
                </label>

                <button type="submit"
                        class="btn-primary w-full py-3 bg-primary text-on-primary rounded-xl font-semibold text-sm
                               shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30
                               active:scale-[0.98] transition-all duration-150
                               focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-2
                               flex items-center justify-center gap-2">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>

                <?= csrf_field() ?>
            </form>
        </div>
    </div>

    <script>
    // Clear any cached session (logout cleanup)
    if (typeof clearAuthSession === 'function') clearAuthSession();

    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility_off';
        }
    }
    </script>
</body>
</html>
