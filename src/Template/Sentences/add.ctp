<!-- File: src/Template/Articles/index.ctp -->

<h1>Sent Sentences</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Sentence</th>
    </tr>

    <!-- Here is where we iterate through our $sentences query object, printing out article info -->

    <?php foreach ($sentences as $sentence): ?>
    <tr>
        <td><?= $sentence->id ?></td>
        <td>
            <?= $sentence->sentence ?>
        </td>
    </tr>
    <?php return debug($sentence);?>
    <?php endforeach; ?>
</table>
