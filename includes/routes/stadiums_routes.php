<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/StadiumModel.php';


function handleGetAllStadiums(Request $request, Response $response, array $args) {
    $stadium_info = array();
    
    $response_data = array();
    $response_code = HTTP_OK;
    $stadium_model = new StadiumModel();

    $stadium_info = $stadium_model->getAll();
        
    $requested_format = $request->getHeader('Accept');  
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($stadium_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetStadiumFromTeam(Request $request, Response $response, array $args) {
    $stadium_info = array();

    $response_data = array();
    $response_code = HTTP_OK;
    $stadium_model = new StadiumModel();

    $team_id = $args["team_id"];
    
    if (isset($team_id)) {
        
        $stadium_info = $stadium_model->getStadiumFromTeam($team_id);
        if (!$stadium_info) {
            
            $response_data = makeCustomJSONError("resourceNotFound", "Team has no stadium info.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    
     
    $requested_format = $request->getHeader('Accept');  
    if ($requested_format[0] === "application/json") {
        $response_data = json_encode($stadium_info);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    
    
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}