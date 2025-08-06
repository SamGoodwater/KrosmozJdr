<p style="text-align: center;"><img src="https://krosmoz-jdr.fr/storage/logos/logo.webp" width="150px" alt='logo'></p>

<h1>Kromos JDR</h1>
<p>DofusJDR devient KrosmozJDR</p>
<p style="text-align: center;"><img src="https://krosmoz-jdr.fr/storage/documents/vers_krosmozjdr.png" height="100px" alt='changement de nom'></p>

<h2>Le jeu de rôle</h2
        <h3 id="bienvenue-dans-krosmozjdr-l-aventure-pique-dans-l-univers-du-monde-des-douze-">Bienvenue dans
            <strong>KrosmozJDR</strong>, l'aventure épique dans l'univers du monde des Douze !
        </h3>
        <p>Plongez dans le monde des Douze, un univers riche et vibrant issu de l'imaginaire de <em>Dofus</em>, où
            l'aventure, la stratégie et la magie s'entrelacent pour créer une expérience unique de jeu de rôle. Ici,
            chaque partie est une porte ouverte vers des terres fascinantes, des créatures captivantes et des combats
            épiques.</p>
        <h4 id="explorez-des-lieux-mythiques">Explorez des lieux mythiques</h4>
        <p>De la cité lumineuse de Bonta aux mystères d'Astrub, en passant par les plaines sauvages des Craqueleurs et
            les secrets d'Amakna, découvrez un monde vaste et varié où chaque région est le théâtre d'histoires
            légendaires. Préparez-vous à croiser la route des Wabbits malicieux, des Bouftous sauvages, des Chafeurs
            redoutables, et à affronter les puissants maîtres des donjons comme Kardorim, Groloum ou encore le Comte
            Harebourg.</p>
        <h4 id="incarnez-une-classe-iconique">Incarnez une classe iconique</h4>
        <p>Choisissez parmi une grande variété de classes emblématiques : serez-vous un Crâ précis et implacable, un Iop
            téméraire et valeureux, un Osamodas lié aux créatures, ou encore un Eliotrope maître des portails ? Que vous
            soyez un soigneur Eniripsa, un stratège Roublard ou un manipulateur du temps Xélor, chaque classe offre des
            mécaniques uniques et des sorts adaptés à l'univers du jeu de rôle.</p>
        <h4 id="d-veloppez-votre-personnage">Développez votre personnage</h4>
        <p>Grâce à un système de compétences et d'aptitudes inspiré de D&amp;D, personnalisez votre héros pour qu'il
            reflète votre style de jeu. Enrichissez vos stratégies avec des spécialisations uniques, inspirées des
            subtilités des races de D&amp;D, et explorez des gameplays toujours plus variés et profonds.</p>
        <h4 id="un-gameplay-enrichi-et-immersif">Un gameplay enrichi et immersif</h4>
        <p>Notre jeu de rôle fusionne les règles classiques du JdR avec l'univers riche de Dofus, pour offrir une
            expérience immersive et accessible à tous, débutants comme vétérans. Préparez-vous à vivre des quêtes
            épiques, à forger des alliances mémorables, et à écrire votre légende dans un monde en perpétuelle
            évolution.</p>
        <hr>
        <h3 id="-tes-vous-pr-t-rejoindre-l-aventure-">Êtes-vous prêt à rejoindre l'aventure ?</h3>
        <p>Rassemblez vos compagnons, lancez vos dés et partez à la découverte du monde des Douze. Votre destin est
            entre vos mains !</p>

<h2>Contacts</h2>
<p>Une version en ligne :  <a href="https://krosmoz-jdr.fr/" target="\_blank">https://krosmoz-jdr.fr/</a> (En cours de création)</p>
<p>Pour contribuer : <a href="https://project.krosmoz-jdr.fr/#contribuer" target="\_blank">#contribuer (Nextcloud)</a> (En cours de création)</p>
<p>Le discord du projet : <a href="https://discord.gg/XVu4VWFskj" target="\_blank">#XVu4VWFskj</a></p>
<p>Le Github du projet : <a href="https://github.com/SamGoodwater/KrosmozJdr" target="\_blank">SamGoodwater/KrosmozJdr</a></p>

<h2>Le code du projet</h2>
<p>Le projet se base sur un backend Laravel et un frontend VueJS avec Invertia pour faire l'interface entre les deux.</p>
<p>La partie css est géré en scss avec le framework TailwindCss</p>
<p>La donnée de Dofus sont extraites automatiquement du site <a href="https://dofusdb.fr/fr/" target='\_blank'>Dofusdb</a></p>

<h2>🛠️ Outils de développement</h2>

<h3>🎭 Scripts Playwright</h3>
<p>Des outils d'automatisation pour les tests et le développement sont disponibles dans le dossier <code>playwright/</code>.</p>

<h4>Utilisation rapide :</h4>
```bash
# Navigation rapide vers localhost:8000
node playwright/run.js nav

# Capture d'écran rapide
node playwright/run.js ss ma-capture.png

# Test de connexion
node playwright/run.js login http://localhost:8000 user@test.com password123

# Afficher l'aide
node playwright/run.js help
```

<p>📖 <a href="playwright/README.md">Documentation complète des outils Playwright</a></p>
