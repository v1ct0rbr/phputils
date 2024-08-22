<!DOCTYPE html>
<html>
<head>
    <title>SHA-256</title>
</head>
<body>
    <form method="post">
        <label for="password">Senha:</label>
        <input type="text" name="password" value="<?php echo isset($POST["password"]) ? $_POST["password"] : "" ?>"  id="password">
        <label for="palavra_passe">Palavra-passe:</label>
        <input type="text" name="palavra_passe" value="<?php echo isset($_POST["palavra_passe"]) ? $_POST["palavra_passe"]: "" ?>" id="palavra_passe">
        <button type="submit">Criptografar</button>
    </form>
</body>
</html>

<?php


// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém o valor do campo de senha do formulário
    $password = $_POST["password"];
    $palavra_passe = $_POST["palavra_passe"];

    // Chama a função encryptPassword para criptografar a senha
    $encryptedPassword = encryptPassword($password, $palavra_passe);

    // Exibe o resultado na tela
    echo "Senha criptografada: " . $encryptedPassword;
}

// Função para criptografar a senha
function encryptPassword($password, $palavra_passe) {
    return hash("sha256", $palavra_passe . hash("sha256", $password));
}

?>