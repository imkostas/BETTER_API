<?php

class CountryController extends BaseController
{
	// Create action for creating countries
	public function createAction()
	{
		// Get json body
		if($json = $this->request->getJsonRawBody())
		{
			// Authenticate API key
			if(!isset($json->api_key) || $this->authenticate($json->api_key))
			{
				return $this->response(400, 'Bad Request', ['error' => ['API key is incorrect or missing']]);
			}
			
			// Initialize country
			$country = new Country();
			$country->name = $json->name;

			// Create country
			if($country->create() == true)
			{
				// Return created
				return $this->response(201, 'Created', ['response' => ['country' => $country]]);
			}

            // Return conflict
            return $this->response(409, 'Conflict', ['error' => $this->fetchErrors($country)]);
		}

        // Return bad request
        return $this->response(400, 'Bad Request', ['error' => ['Request body should be sent as JSON']]);
	}
}