<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>

    <!--font awesome link for icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!--CSS file link-->
    <link rel="stylesheet" href="../css/admin_products.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>!</h1>

    <section class="add_products">

        <h1 class="title">Add New Products</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="flex">
                <input type="text" name="name" placeholder="Enter product name" class="box" required>
                <select name="category" class="box" required>
                    <option value="" disabled selected>Select category</option>
                    <option value="vegetables">Vegetables</option>
                    <option value="fruits">Fruits</option>
                    <option value="seafood">Seafood</option>
                    <option value="proteins">Proteins</option>
                    <option value="dairy">Dairy</option>
                    <option value="seasonal">Seasonal Produce</option>
                </select>
            </div>
            <div class="inputBox">
                <input type="number" min="0" name="price" required placeholder="Enter product price" class="box">
                <input type="file" required class="box" accept="image/jpg, image/jpeg, image/png">
            </div> 
            <textarea name="details" class="box" cols="30" rows="10" placeholder="Enter product details"></textarea>
            <input type="submit" class="btn" value="Add Product" name="add_product">
        </form>


    </section>

</body>
</html>