<!-- File: src/Template/Articles/index.ctp -->

<h1>Blog articles</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Body</th>
        <th>Created</th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($articles as $article): ?>
    <tr>
        <td><?= $article->id ?></td>
        <td>
            <?= $this->Html->link(
              $article->title,
                ['action' => 'view',
              $article->id]
            ); ?>
        </td>
        <td><?= $article->body ?></td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
        <td>
          <?php echo $this->Html->link('Edit',['action' => 'edit',
        $article->id]); ?>
        </td>
        <td>
          <?php echo $this->Html->link('Delete',['action' => 'delete',
        $article->id]); ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
