<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>register form</h1>
    <form action="includes/signup.php" method="POST">
        <input type="text" placeholder="username" name="username">
          <input type="text" placeholder="email" name="email">
        <input type="text" placeholder="password" name="password">
        <button >SIGNUP</button>
    </form>

    <h1>login form</h1>
    <form action="includes/login.php" method="POST">
        <input type="text" placeholder="email" name="email">
        <input type="text" placeholder="password" name="password">
        <button >login</button>
    </form>
</body>
</html>