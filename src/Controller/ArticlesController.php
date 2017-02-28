<?php

namespace App\Controller;

class ArticlesController extends AppController
{

  public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

  public function index()
  {
       $this->set('articles', $this->Articles->find('all'));
  }

  public function view($id)
  {
      $article = $this->Articles->get($id);
      $this->set(compact('article'));
  }

  public function edit($id)
    {
      $article = $this->Articles->get($id);
      if ($this->request->is(['post', 'put'])) {
          $this->Articles->patchEntity($article, $this->request->getData());
          if ($this->Articles->save($article)) {
              $this->Flash->success(__('Your article has been updated.'));
              return $this->redirect(['action' => 'index']);
          }
          $this->Flash->error(__('Unable to update your article.'));
      }

      $this->set('article', $article);
    }

  public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set('article', $article);
    }

    public function delete($id)
    {

        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article with id: {0} has been deleted.', h($id)));
            return $this->redirect(['action' => 'index']);
        }
    }
}

 ?>
