<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Mini ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen bg-gray-100">
    <div class="h-full w-full flex justify-center items-center">
        <div class="bg-white shadow-lg rounded-lg p-8 min-w-96">
            <h2 class="text-2xl text-center font-bold mb-4">Login</h2>
            <form id="login-form" method="post">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input type="text" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                            <i id="eyeOffIcon" class="fa-solid fa-eye-slash" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-md cursor-pointer">Login</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const eyeIcon = $('#eyeIcon');
                const eyeOffIcon = $('#eyeOffIcon');
                
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                
                if (type === 'password') {
                    eyeIcon.show();
                    eyeOffIcon.hide();
                } else {
                    eyeIcon.hide();
                    eyeOffIcon.show();
                }
            });
        });

        $("#login-form").on("submit", function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed').text('Logging in...');

            let formData = new FormData();
            formData.append("email", $("#email").val());
            formData.append("password", $("#password").val());
            formData.append("_token", $("input[name=_token]").val());

            $.ajax({
                url: '{{ route("login.post") }}',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Toastify({
                            text: response.message,
                            duration: 2000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#16a34a",
                        }).showToast();

                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    } else {
                        submitBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text('Login');
                        
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#e11d48",
                        }).showToast();
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text('Login');

                    let errorMsg = "Terjadi kesalahan pada server.";
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const firstErrorKey = Object.keys(xhr.responseJSON.errors)[0];
                            if (firstErrorKey && xhr.responseJSON.errors[firstErrorKey][0]) {
                                errorMsg = xhr.responseJSON.errors[firstErrorKey][0];
                            }
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }

                    Toastify({
                        text: errorMsg,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#e11d48",
                    }).showToast();
                }
            });
        });
    </script>
</body>
</html>