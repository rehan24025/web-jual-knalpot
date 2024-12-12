<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <style>
        /* CSS untuk halaman Order Success */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: #4CAF50;
            font-size: 2em;
            margin-bottom: 10px;
            text-align: center;
        }

        p {
            font-size: 1.1em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            font-size: 1em;
            color: #fff;
            text-decoration: none;
            background-color: #4CAF50;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 30px 50px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Berhasil!</h1>
        <p>Terima kasih telah berbelanja. Pesanan Anda telah diproses.</p>
        <a href="../user/order_history.php" class="button">lihat history order</a>
        <a href="../product.php" class="button">Kembali ke Beranda</a>
    </div>
</body>
</html>
