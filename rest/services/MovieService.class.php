<?php

require_once __DIR__ . '/BaseService.class.php';
require_once __DIR__ . '/../dao/MovieDao.class.php';
require_once __DIR__ . '/../dao/UserDao.class.php';

class MovieService extends BaseService{
    private $user_dao;

    public function __construct(){
        parent::__construct(new MovieDao());
        $this->user_dao = new UserDao();
    }

    public function get_all_movies(){
        return $this->dao->get_all_movies();
    }

    public function get_movie_by_id($id){
        return $this->dao->get_movie_by_id($id);
    }

    public function add_movie($movie)
    {
    return $this->dao->add_movie($movie);
    }

    public function update_movie($id, $movie){
        return $this->dao->update_movie($id, $movie);
    }

    public function delete_movie($id){
        $movie = $this->dao->get_movie_by_id($id);
    if (!$movie) {
        throw new Exception("Movie not found");
    }

    // Call the DAO method to delete the movie
    return $this->dao->delete_movie($id);
    }

    public function follow_movie($user_id, $movie_id){
        return $this->dao->follow_movie($user_id, $movie_id);
    }

    public function get_followed_movies_by_user_id($user_id){
        return $this->dao->get_followed_movies_by_user_id($user_id);
    }
}

?>