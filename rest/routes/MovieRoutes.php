<?php

/**
 * @OA\Get(
 *     path="/movies",
 *     summary="Retrieve all movies from the movies table",
 *     description="This endpoint returns a list of all movies from the movies table.",
 *     tags={"movie"},
 *     @OA\Response(
 *         response=200,
 *         description="List of movies",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Movie")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not found"
 *     ),
 *     security={
 *         {"jwt": {}}
 *     }
 * )
 */

Flight::route('GET /movies', function(){

    $movie = Flight::request()->query->getData();
    $user = Flight::get('user');

    // Flight::json(Flight::movieService()->get_all($user));
    
  Flight::json(Flight::movieService()->get_all_movies($user));
});

Flight::route('GET /movies/@id', function($id){

    $user = Flight::get('user');
  
    Flight::json(Flight::movieService()->get_movie_by_id(Flight::get('user'), $id));
});

Flight::route('POST /movies', function(){
    Flight::json(Flight::movieService()->add(Flight::get('user'), Flight::request()->data->getData()));
});

  Flight::route('PUT /movies/@id', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::movieService()->update(Flight::get('user'), $id, $data));
});

  Flight::route('DELETE /movies/@id', function($id){
    Flight::movieService()->delete(Flight::get('user'), $id);
    Flight::json(["movie" => "deleted"]);
});
?>