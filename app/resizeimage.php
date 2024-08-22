<!DOCTYPE html>
<html>
<head>
    <title>Redimensionar Imagem</title>
</head>

<!-- criate a multipart form -->
<body>
    <form method="post" enctype="multipart/form-data">
        <label for="image">Imagem:</label>
        <input type="file" name="image" id="image">
        <select name="append_text">
            <option value="">normal</option>
            <option value="placeholder">placeholder</option>  
            <option value="large">large</option>  
        </select>
        <label for="width">Largura:</label>
        <input type="text" name="width" value="<?php if ($_SERVER["REQUEST_METHOD"] == "POST") echo $_POST['width']; ?>" id="width">
        <label for="height">Altura:</label>
        <input type="text" name="height" value="<?php if ($_SERVER["REQUEST_METHOD"] == "POST") echo $_POST['height']; ?>" id="height">
        <button type="submit">Redimensionar</button>
    </form>

<?php 
$ROOT_DIR = $_SERVER['DOCUMENT_ROOT'];

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém o arquivo enviado
    $image = $_FILES["image"];
    
    $imageType = $image["type"];
    $extensions = array("image/jpeg", "image/jpg", "image/png", "image/gif");
    if (!in_array($imageType, $extensions)) {
        echo "Apenas arquivos JPEG, JPG, PNG e GIF são permitidos.";
        exit;
    }
    $imageExtensionString = explode("/", $imageType)[1];
    // Obtém a largura e a altura desejadas
    $width = $_POST["width"];
    $height = $_POST["height"];
    $append_text = $_POST["append_text"];
    // Chama a função resizeImage para redimensionar a imagem
    $new_image_name = $image["name"].'_'.$append_text.'_'.$height.'x'.$width.'.'.$imageExtensionString;
    resizeImage($image["tmp_name"], $width, $height, $ROOT_DIR.'/resizedImages/'.$new_image_name);
    // Exibe a imagem redimensionada na tela
    echo "<img src='resizedImages/".$new_image_name."'>";
}

function resizeImage($file, $width, $height, $output) {
    // Tente criar uma imagem a partir do arquivo
    $imageInfo = getimagesize($file);
    $imageType = $imageInfo[2];
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_JPEG2000:
            $image = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($file);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($file);
            break;
        default:
            die("Tipo de imagem não suportado: $file.");
    }
    

    // Verifique se a criação da imagem foi bem-sucedida
    if ($image === false) {
        die("Falha ao criar imagem a partir do arquivo $file.");
    }

    // Obtenha as dimensões da imagem original
    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);

    // Crie uma nova imagem com as dimensões especificadas
    $newImage = imagecreatetruecolor($width, $height);

    // Redimensione a imagem original para a nova imagem
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

    // Salve a nova imagem no local especificado
    imagejpeg($newImage, $output);

    // Libere a memória associada às imagens
    imagedestroy($image);
    imagedestroy($newImage);
}

    ?>