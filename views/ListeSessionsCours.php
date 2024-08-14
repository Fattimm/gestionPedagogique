<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecole 221</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/fr.js"></script>
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
                    <a href="/ListeCours" class="flex items-center space-x-2">
                        <i class="fas fa-book"></i>
                        <span>Listes des Cours</span>
                    </a>
                </li>
                <li class="p-4 hover:bg-gray-700">
                    <a href="#" class="flex items-center space-x-2">
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
                <span class="text-gray-700"><?php echo htmlspecialchars($professeur['prenom'] . ' ' . $professeur['nom']); ?></span>
                <a href="/logout" class="px-4 py-2 bg-blue-600 text-white rounded">Déconnexion</a>
            </div>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold">Listes des Sessions</h1>
        </div>

        <!-- Calendar Container -->
        <div id="calendar" class="bg-white p-4 border border-gray-200 rounded-lg"></div>

        <!-- FullCalendar JS -->
        <script>
            $(document).ready(function() {
                var events = <?php echo json_encode($sessions); ?>;

                $('#calendar').fullCalendar({
                    defaultView: 'agendaDay', // Afficher la vue quotidienne
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'agendaDay,agendaWeek,month'
                    },
                    locale: 'fr', // Configurer en français
                    minTime: '08:00:00', // Heure de début de l'affichage
                    maxTime: '20:00:00', // Heure de fin de l'affichage
                    events: events.map(function(event) {
                        return {
                            title: event.title,
                            start: event.date + 'T' + event.heureDebut,
                            end: event.date + 'T' + event.heureFin,
                            color: event.color
                        };
                    }),
                    noEventsMessage: 'Aucun événement à afficher',
                    timeFormat: 'H:mm', // Format de l'heure en 24 heures
                    eventClick: function(calEvent, jsEvent, view) {
                        // Vérifier la couleur de l'événement pour éviter la double annulation
                        if (calEvent.color === 'red') {
                            alert('Cette session a déjà été annulée.');
                            return;
                        }
                        $('#sessionId').val(calEvent.id); // Assigner l'ID de la session à un champ caché
                        $('#cancelModal').removeClass('hidden'); // Afficher le modale
                    }
                });
                $('#cancelForm').on('submit', function(e) {
                    e.preventDefault();

                    var sessionId = $('#sessionId').val();
                    var reason = $('#reason').val();

                    $.ajax({
                        url: '/AnnulerSession', // Assurez-vous que ce chemin est correct
                        type: 'POST',
                        data: {
                            id: sessionId,
                            reason: reason
                        },
                        success: function(response) {
                            // Traiter la réponse du serveur ici, si nécessaire
                            $('#cancelModal').addClass('hidden'); // Cacher le modal
                            $('#calendar').fullCalendar('refetchEvents'); // Recharger les événements
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Traiter les erreurs ici
                            console.error('Erreur lors de l\'annulation :', textStatus, errorThrown);
                        }
                    });
                });

                // Gestion du bouton d'annulation du modal
                $('#cancelButton').on('click', function() {
                    $('#cancelModal').addClass('hidden'); // Cacher le modal
                });

            });
        </script>
    </div>
    <!-- Modal demande annulation -->
    <div id="cancelModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg w-1/3 relative z-60">
            <h2 class="text-xl font-bold mb-4">Demande d'annulation</h2>
            <form id="cancelForm">
                <input type="hidden" id="sessionId">
                <label for="reason" class="block text-sm font-medium text-gray-700">Motif de l'annulation</label>
                <textarea id="reason" name="reason" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required></textarea>
                <p class="mt-2 text-gray-600">Êtes-vous sûr de vouloir annuler ce cours ?</p>
                <div class="flex justify-end mt-4">
                    <button type="button" id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Annuler</button>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Confirmer</button>
                </div>
            </form>
        </div>
    </div>

    </div>

</body>

</html>