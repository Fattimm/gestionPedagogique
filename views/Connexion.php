<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - École 221</title>
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-blue-50 flex items-center justify-center min-h-screen">
  <div class="bg-blue-900 text-white shadow-md rounded-lg p-8 max-w-md w-full">
    <div class="mb-4">
      <h1 class="text-2xl font-semibold">ECOLE 221</h1>
    </div>
    <div class="bg-white shadow-md rounded-lg p-8">
      <h2 class="text-xl font-semibold text-gray-800">Connexion</h2>
      <form method="post" action="/Connexion">
        <?php if (!empty($errors)) : ?>
          <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
            <?php foreach ($errors as $error) : ?>
              <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="mb-4">
          <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="email" name="email" class="text-black w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Entrez votre email">
        </div>
        <div class="mb-4">
          <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Mot de passe</label>
          <input type="password" id="password" name="password" class="text-black w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Entrez votre mot de passe">
        </div>
        <div class="flex items-center justify-between">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Se connecter</button>
          <a href="#" class="text-sm text-gray-600 hover:underline">Mot de passe oublié?</a>
        </div>
      </form>
    </div>
  </div>
</body>

</html>