<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Chave Privada e Certificado</title>
</head>
<body>
    <form method="post">
        <label for="commonName">Common Name (CN):</label><br>
        <input type="text" id="commonName" name="commonName" required><br><br>

        <label for="countryName">Country Name (C):</label><br>
        <input type="text" id="countryName" name="countryName" maxlength="2" required><br><br>

        <label for="state">State or Province Name (ST):</label><br>
        <input type="text" id="state" name="state" required><br><br>

        <label for="locality">Locality Name (L):</label><br>
        <input type="text" id="locality" name="locality" required><br><br>

        <label for="organization">Organization Name (O):</label><br>
        <input type="text" id="organization" name="organization" required><br><br>

        <label for="organizationalUnit">Organizational Unit Name (OU):</label><br>
        <input type="text" id="organizationalUnit" name="organizationalUnit" required><br><br>

        <label for="p12Password">Password for P12:</label><br>
        <input type="password" id="p12Password" name="p12Password" required><br><br>

        <button type="submit" name="generate">Gerar e Exportar P12</button>
    </form>

    <?php
    if (isset($_POST['generate'])) {
        $dn = array(
            "countryName" => $_POST['countryName'],
            "stateOrProvinceName" => $_POST['state'],
            "localityName" => $_POST['locality'],
            "organizationName" => $_POST['organization'],
            "organizationalUnitName" => $_POST['organizationalUnit'],
            "commonName" => $_POST['commonName']
        );

        // Gera uma nova chave privada de 2048 bits
        $privateKey = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        // Gera um pedido de assinatura de certificado (CSR)
        $csr = openssl_csr_new($dn, $privateKey, array('digest_alg' => 'sha256'));

        // Auto-assina o CSR para gerar um certificado X.509
        $cert = openssl_csr_sign($csr, null, $privateKey, 365, array('digest_alg' => 'sha256'));

        // Defina a senha do P12
        $p12Password = $_POST['p12Password'];

        // Exporta a chave privada e o certificado para um arquivo P12
        $p12 = null;
        openssl_pkcs12_export($cert, $p12, $privateKey, $p12Password);

        // Salva o arquivo P12

        $outputDir = 'output/';

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $p12File = $outputDir . 'certificado.p12';
        file_put_contents($p12File, $p12);

        echo "<p>O arquivo P12 foi gerado com sucesso. <a href='$p12File'>Clique aqui para baixar</a></p>";

        // Não é mais necessário liberar recursos explicitamente em PHP 8.0+
        // openssl_x509_free($cert);
        // openssl_pkey_free($privateKey);
    }
    ?>
</body>
</html>
