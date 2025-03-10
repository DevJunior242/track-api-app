<!DOCTYPE html>
<html>
<head>
    <title>Budget Dépassé</title>
</head>
<body>
    <h1>Attention !</h1>
    <p>Vous avez dépassé votre budget pour la catégorie {{ $category }}.</p>
    <p>Budget limite : {{ $budgetLimit }}</p>
    <p>Total dépensé : {{ $totaldep }}</p>
    <p>Montant restant : {{ $remaining }}</p>
</body>
</html>
