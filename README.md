# Wordle PHP

Une implémentation console du jeu Wordle en PHP avec couverture de test via PHPUnit.

## Prérequis

- PHP >= 8.0 (Pour Windows, des solutions comme [Wamp](https://www.wampserver.com/) ou [Xampp](https://www.apachefriends.org/fr/index.html) existe.)
- [PHP Composer](https://getcomposer.org/download/) installé après avoir mis en place PHP.
- [PHPUnit](https://phpunit.de/) installé globalement ou via Composer

## Exécution du projet web

Lancez votre serveur web (Wamp ou Xampp si vous êtes sous windows) et accéder à la page localhost, telle que :

```bash
http://localhost/<nom du projet>
```

## Exécution du test unit
Accédez par la console ou le terminal à la racine du projet, quand cela est fais, exécutez la commande suivante :

```bash
$ ./vendor/bin/phpunit tests
```

Dans le cas ou vous rajouteriez des fichiers de test supplémentaire (Ces derniers doivent porter le nom d'un fichier présent dans le **src**), exécutez cette commande :

```bash
$ composer dump-autoload
```

## Ce que le code gère
- Validation des mots (5 lettres, lettres uniquement)
- Feedback par lettre : correct, mal placé, incorrect
- Gestion des tentatives (6 max)
- Dictionnaire intégré
- Couverture de test > 80 %

## Structure des tests

Les tests unitaires sont organisés dans le dossier `tests/`.

### Tests déterminant si un mot est valide ou non:
```
├── guessTheWord()
│ ├── Rejet des mots en dessous de 5 lettres
│ ├── Rejet des mots avec des éléments décimals
│ └── Accepte les mots de 5 charactères
```

### Tests d'évaluation du mot à deviner:
```
├── guessTheWord()
│ ├── Retourne "correct" quand le mot est similaire
│ ├── Retourne "partially correct" quand le mot a des éléments correctes, mais que des éléments sont incorrecte ou pas au bon endroit.
│ ├── Retourne "incorrect" quand aucune lettre n'est valide
│ └── Gère les lettres en double mais dont seul est correcte (par exemple)
```

### Tests des statistique du joueur:
```
├── guessTheWord()
│ ├── Mets à jour le nombre de tentative après chaque essaies
│ ├── Arrête le jeu après avoir atteint le nombre maximum de tentatives
│ └── Retourne le nombre de tentatives restantes
```

### Tests d'utilitaires:
```
├── generateGuess()
│ └── Génère des mot de cinq lettres correctement
```