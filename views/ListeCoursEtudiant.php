<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecole 221</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

</head>

<body class="bg-blue-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen">
        <div class="p-4">
            <h1 class="text-2xl font-bold">Ecole 221</h1>
        </div>
        <nav class="mt-6">
            <ul>
                <li class="p-4 hover:bg-gray-700">
                    <a href="#" class="flex items-center space-x-2">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboards</span>
                    </a>
                </li>
                <li class="p-4 hover:bg-gray-700">
                    <a href="/ListeCoursEtudiant" class="flex items-center space-x-2">
                        <i class="fas fa-book"></i>
                        <span>Listes des Cours</span>
                    </a>
                </li>
                <li class="p-4 hover:bg-gray-700">
                    <a href="/ListeSessionsCoursEtudiant" class="flex items-center space-x-2">
                        <i class="fas fa-calendar"></i>
                        <span>Listes des Sessions</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex-1">
                <input type="text" placeholder="Recherche..." class="p-2 border rounded w-full">
            </div>
            <div class="flex items-center space-x-4">
                <img src="https://via.placeholder.com/40" alt="User" class="w-10 h-10 rounded-full">
                <span class="text-gray-700"><?php if (isset($etudiant)) : ?>
                    <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>
                    <?php else : ?>
                        Utilisateur non identifié
                    <?php endif; ?></span>
                <a href="/logout" class="px-4 py-2 bg-blue-600 text-white rounded">Déconnexion</a>
            </div>
        </div>
        <div>
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Listes des Cours</h1>
            </div>

            <!-- Filter Form -->
            <form class="mb-6" method="get" action="/ListeCoursEtudiant">
                <div class="max-w-lg mx-0">
                    <div class="flex items-start gap-4">
                        <!-- Filtrage par module -->
                        <div class="flex flex-col w-1/4">
                            <label for="module" class="text-gray-700 font-medium">Module:</label>
                            <select id="module" name="module" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tous les Modules</option>
                                <?php foreach ($modules as $module) : ?>
                                    <option value="<?php echo htmlspecialchars($module['id']); ?>" <?php echo isset($selectedModule) && $selectedModule == $module['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($module['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtrage par lour/semaine -->
                        <div class="flex flex-col w-1/4">
                            <label for="periode" class="text-gray-700 font-medium">Période:</label>
                            <select id="periode" name="periode" class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Toute la période</option>
                                <?php foreach ($periodes as $periodeOption) : ?>
                                    <option value="<?php echo htmlspecialchars($periodeOption['value']); ?>" <?php echo isset($selectedPeriode) && $selectedPeriode == $periodeOption['value'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($periodeOption['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex flex-col justify-start mt-5">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>
                </div>
            </form>


            <!-- Table -->
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Libelle</th>
                        <th class="py-2 px-4 border-b">Heure Global</th>
                        <th class="py-2 px-4 border-b">Date Debut</th>
                        <th class="py-2 px-4 border-b">Date Fin</th>
                        <th class="py-2 px-4 border-b">Professeur</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cours) && is_array($cours)) : ?>
                        <?php foreach ($cours as $course) : ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['libelle']) ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['heureGlobal']) ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['dateDebut']) ?></td>
                                <td class="py-2 px-4 border-b"><?= htmlspecialchars($course['dateFin']) ?></td>
                                <td class="py-2 px-4 border-b">
                                    <?php
                                    // Vérifiez si les informations du professeur existent
                                    if (isset($professeurs[$course['id']])) {
                                        $professeur = $professeurs[$course['id']];
                                        echo htmlspecialchars($professeur['prenom'] . ' ' . $professeur['nom']);
                                    } else {
                                        echo 'Non défini'; // Valeur par défaut si le professeur n'est pas défini
                                    }
                                    ?> </td>
                                <td class="py-2 px-4 border-b">
                                    <span class="<? $course['status'] === 'Terminé' ? 'text-green-500' : 'text-red-500'; ?>">
                                        <?php echo htmlspecialchars($course['status']); ?>
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <a href="/ListeSessionsCours?coursId=<?php echo htmlspecialchars($course['id']); ?>" class="px-4 py-2 bg-blue-600 text-white rounded">Détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="py-2 px-4 border-b text-center">Aucun cours trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
                <nav aria-label="Navigation des pages">
                    <ul class="flex space-x-2">
                        <!-- Page Précédente -->
                        <?php if ($currentPage > 1) : ?>
                            <li>
                                <a href="?page=<?php echo $currentPage - 1; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                    Précédent
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Numéros des Pages -->
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>" class="px-4 py-2 <?php echo $i == $currentPage ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'; ?> border border-gray-300 rounded-md hover:bg-blue-100 transition duration-200">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Page Suivante -->
                        <?php if ($currentPage < $totalPages) : ?>
                            <li>
                                <a href="?page=<?php echo $currentPage + 1; ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                    Suivant
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</body>

</html>