
<h2>Importer des unités centrales</h2>

<form action="dashboard/tech/unite-centrale/ajouter_csv" method="post" enctype="multipart/form-data">
    <label>Select CSV file:</label>
    <input type="file" name="csv_file" accept=".csv" required>
    <br><br>
    <button type="submit">importer</button>
</form>

<i>les écrans déjà existants seront ignorer</i>