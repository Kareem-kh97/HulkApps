<?php
require_once __DIR__.'/BaseDao.class.php';

class MovieDao extends BaseDao {

  /**
  * constructor of dao class
  *we sets up the movieDao class to perform CRUD operations specifically for movies from the database
  */
  public function __construct() {
    parent::__construct("movies");
  }

  public function get_all_movies() {

    $memcached = new Memcached();
    $memcached->addServer('localhost', 11211);

    // Check if data is in Memcached
    $movies = $memcached->get('movies');
    if ($movies) {
      return $movies;
    }

    $stmt = $this->prepare("SELECT * FROM movies");
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Store the result in Memcached
    $memcached->set('movies', $movies, 60); // expire after 60 seconds

    // Return the result
    return $movies;
  }

  public function get_movie_by_id($id){
    return $this->query_unique('SELECT * FROM movies WHERE id = :id', ['id' => $id]);
  }
  // public function get_movie_by_id($id) {
  //   return $this->get_by_id($id);
  // }

  public function add_movie($entity) {
    $query = "INSERT INTO movies (";
    foreach ($entity as $column => $value) {
      $query .= $column.", ";
    }
    $query = substr($query, 0, -2);
    $query .= ") VALUES (";
    foreach ($entity as $column => $value) {
      $query .= ":".$column.", ";
    }
    $query = substr($query, 0, -2);
    $query .= ")";

    $stmt= $this->prepare($query);
    $stmt->execute($entity); // sql injection prevention
    $entity['id'] = $this->lastInsertId();
    // return $entity;
    return $this->add($entity);
  }

  public function update_movie($id, $entity) {
    $query = "UPDATE movies SET ";
    foreach($entity as $name => $value){
      $query .= $name ."= :". $name. ", ";
    }
    $query = substr($query, 0, -2);
    $query .= " WHERE id = :id";

    $stmt= $this->prepare($query);
    $entity['id'] = $id;
    $stmt->execute($entity);
    // return $entity;
    return $this->update($id, $entity);
  }

  public function delete_movie($id) {

    $stmt = $this->prepare("DELETE FROM movies WHERE id=:id");
    $stmt->bindParam(':id', $id); // SQL injection prevention
    $stmt->execute();
    $this->delete($id);
  }

  /**
   * for the requirement `Users should be able to follow a selected movie`
   */
  // this method allows user to follow a movie, with tow parameters and bind their values to the placholders and execute the method
  //this function is for a user to follow a movie
  public function follow_movie($user_id, $movie_id) {
    $query = "INSERT INTO user_movie_follows (user_id, movie_id) VALUES (:user_id, :movie_id)";
    $stmt = $this->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':movie_id', $movie_id);
    $stmt->execute();
}
/**
 * retreving the the `user_movie_follows` table to retrieve all movie ids followed by a specific user. 
 * retrieve movies that a user is following
 */
public function get_followed_movies_by_user_id($user_id) {
    $query = "SELECT * FROM user_movie_follows JOIN movies ON user_movie_follows.movie_id = movies.id WHERE user_id = :user_id";
    $stmt = $this->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

?>