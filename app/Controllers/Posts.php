<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Posts extends ResourceController
{
    // protected $modelName = 'App\Models\PostModel';
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new \App\Models\PostModel();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $data = $this->model->orderBy('id', 'DESC')->findAll();
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $record = $this->model->find($id);
        if (!$record) {
            # code...
            return $this->failNotFound(sprintf(
                'post with id %d not found',
                $id
            ));
        }

        return $this->respond($record);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $data = $this->request->getPost();
        if(!$data){
            // $data = $this->request->getJson(); // stdClass
            // var_dump($data);
            $data = $this->request->getBody(); // String
            $data = json_decode($data, true);
        }
        $data['slug'] = $data['title'];
        $data['status'] = "1";
        
        $post = new \App\Entities\Post($data);
        if (!$this->model->save($post)) {
            return $this->fail($this->model->errors());
        }

        return $this->respondCreated($post, 'post created');
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $record = $this->model->find($id);
        if (!$record) {
            # code...
            return $this->failNotFound(sprintf(
                'post with id %d not found',
                $id
            ));
        }

        $data = $this->request->getRawInput();
        $data['id'] = $id;

        $post = new \App\Entities\Post($data);

        $validations = [
            'title' => 'required|alpha_numeric_space|min_length[3]|max_length[255]',
            'content' => 'required',
            'status' => 'required'
        ];

        if (isset($data['title'])) {
            if ($data['title'] != $record->title) {
                $validations['title'] = 'required|alpha_numeric_space|min_length[3]|max_length[255]|is_unique[posts.title]';
            }
        }

        $this->model->validationRules = $validations;

        if (!$this->model->save($post)) {
            # code...
            return $this->fail($this->model->errors());
        }

        return $this->respond($data, 200, 'post updated');
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $delete = $this->model->delete($id);
        if (
            $this->model->db->affectedRows() === 0
        ) {
            return $this->failNotFound(sprintf(
                'post with id %id not found or already deleted',
                $id
            ));
        }

        return $this->respondDeleted(['id' => $id], 'post deleted');
    }

    public function options()
    {
        return $this->response->setStatusCode(200);
    }
}