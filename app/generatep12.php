<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Chave Privada e Certificado</title>
</head>
<body>
    <form id="certForm">
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

        <button type="button" id="generateBtn">Gerar Chave Privada e Certificado</button>
        <button type="button" id="exportP12Btn">Exportar para P12</button>
    </form>

    <h3>Chave Privada:</h3>
    <textarea id="privateKey" rows="10" cols="60" readonly></textarea>

    <h3>Certificado:</h3>
    <textarea id="certificate" rows="10" cols="60" readonly></textarea>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script>
        let keypair;
        let cert;

        document.getElementById('generateBtn').addEventListener('click', function() {
            // Obtém os valores do formulário
            const commonName = document.getElementById('commonName').value;
            const countryName = document.getElementById('countryName').value;
            const state = document.getElementById('state').value;
            const locality = document.getElementById('locality').value;
            const organization = document.getElementById('organization').value;
            const organizationalUnit = document.getElementById('organizationalUnit').value;

            // Gera a chave privada
            keypair = forge.pki.rsa.generateKeyPair(2048);
            const privateKeyPem = forge.pki.privateKeyToPem(keypair.privateKey);

            // Define os dados do certificado
            cert = forge.pki.createCertificate();
            cert.publicKey = keypair.publicKey;
            cert.serialNumber = '01';
            cert.validity.notBefore = new Date();
            cert.validity.notAfter = new Date();
            cert.validity.notAfter.setFullYear(cert.validity.notBefore.getFullYear() + 1);

            const attrs = [{
                name: 'commonName',
                value: commonName
            }, {
                name: 'countryName',
                value: countryName
            }, {
                shortName: 'ST',
                value: state
            }, {
                name: 'localityName',
                value: locality
            }, {
                name: 'organizationName',
                value: organization
            }, {
                shortName: 'OU',
                value: organizationalUnit
            }];
            cert.setSubject(attrs);
            cert.setIssuer(attrs);

            // Assina o certificado
            cert.sign(keypair.privateKey);

            const certPem = forge.pki.certificateToPem(cert);

            // Exibe a chave privada e o certificado nos campos de texto
            document.getElementById('privateKey').value = privateKeyPem;
            document.getElementById('certificate').value = certPem;
        });

        document.getElementById('exportP12Btn').addEventListener('click', function() {
            const password = document.getElementById('p12Password').value;

            if (!keypair || !cert) {
                alert('Por favor, gere a chave privada e o certificado primeiro.');
                return;
            }

            const p12Asn1 = forge.pkcs12.toPkcs12Asn1(keypair.privateKey, cert, password);
            const p12Der = forge.asn1.toDer(p12Asn1).getBytes();
            const p12Blob = new Blob([forge.util.binary.raw.decode(p12Der)], { type: 'application/x-pkcs12' });

            const link = document.createElement('a');
            link.href = URL.createObjectURL(p12Blob);
            link.download = 'certificate.p12';
            link.click();
        });
    </script>
</body>
</html>
