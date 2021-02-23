<div class="container">
    <form class="form-signin" method="POST" action="">
        <h2 class="form-signin-heading">Please register</h2>

        <label for="inputEmail" class="sr-only">Email address</label>
        <input
            type="email"
            id="inputEmail"
            class="form-control"
            placeholder="Email address"
            name="email"
            value="<?php echo (isset($_POST['email'])) ? $_POST['email'] : ''; ?>"
        >

        <label for="inputPassword" class="sr-only">Password</label>
        <input
            type="password"
            id="inputPassword"
            class="form-control"
            placeholder="Password"
            name="pass"
            value="<?php echo (isset($_POST['pass'])) ? $_POST['pass'] : ''; ?>"
        >

        <label for="inputPasswordConfirm" class="sr-only">Password</label>
        <input
            type="password"
            id="inputPasswordConfirm"
            class="form-control"
            placeholder="Password Confirm"
            name="pass_confirm"
            value="<?php echo (isset($_POST['pass_confirm'])) ? $_POST['pass_confirm'] : ''; ?>"
        >

        <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
    </form>
</div>