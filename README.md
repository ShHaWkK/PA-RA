# NO MORE WASTE

## Description

NO MORE WASTE est une association humanitaire de lutte contre le gaspillage. Ce projet vise à moderniser leur système d'information. Vous pouvez trouver plus d'informations dans la documentation.

## Structure du Projet

### API

- **FastAPI** pour le backend
- **SQLAlchemy** pour l'ORM
- **Pydantic** pour la validation des données
- **Uvicorn** pour le serveur ASGI
- **Gunicorn** pour le déploiement en production
- **Redoc** pour la documentation API
- **Swagger** pour tester les endpoints

### Front-end

- **React** pour le front-end
- **Redux** pour la gestion de l'état (potentiellement)

## Configuration et Installation

### Prérequis

- **Docker** et **Docker Compose** installés sur votre machine

### Installation

1. Clonez ce repository sur votre machine locale :

    ```bash
    git clone https://github.com/votre-utilisateur/no-more-waste.git
    cd no-more-waste
    ```

2. Créez un fichier `.env` dans le répertoire `API` et ajoutez les variables d'environnement suivantes :
### Par exemple : 
    ```env
    DATABASE_URL=mysql+pymysql://Utilisateur:password@host:port/Database
    ```

3. Démarrez les conteneurs Docker :

    ```bash
    docker-compose up --build
    ```

4. Accédez à l'API à l'adresse `http://localhost:8000` et à la documentation Redoc à `http://localhost:8000/redoc`.

### Structure des Répertoires

- **API** : Contient le code source de l'API
  - **Controllers** : Contient les contrôleurs pour les différentes entités
  - **Models** : Contient les modèles SQLAlchemy
  - **Schemas** : Contient les modèles Pydantic pour la validation des données
  - **config.py** : Configuration de l'application
  - **database.py** : Configuration de la base de données
  - **main.py** : Point d'entrée de l'application FastAPI
- **front** : Contient le code source du front-end React
- **init.sql** : Script d'initialisation de la base de données
- **docker-compose.yml** : Fichier de configuration Docker Compose

### Swagger UI : 
![image](https://github.com/ShHaWkK/PA-RA/assets/51519814/e598b1aa-c2ec-439b-a0b3-4e60dc7ef591)

### Redoc : 
![image](https://github.com/ShHaWkK/PA-RA/assets/51519814/602ccdcc-c608-4a90-b17a-8161c51469a4)
