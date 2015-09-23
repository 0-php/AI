<?php	
/**
   Copyright 2013 AlchemyAPI

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

	require_once 'alchemyapi.php';
	$alchemyapi = new AlchemyAPI();
	

	$demo_text = 'Yesterday dumb Bob destroyed my fancy iPhone in beautiful Denver, Colorado. I guess I will have to head over to the Apple Store and buy a new one.';
	$demo_url = 'http://www.npr.org/2013/11/26/247336038/dont-stuff-the-turkey-and-other-tips-from-americas-test-kitchen';
	$demo_html = '<html><head><title>PHP Demo | AlchemyAPI</title></head><body><h1>Did you know that AlchemyAPI works on HTML?</h1><p>Well, you do now.</p></body></html>';

	echo '<br>';
	echo '<br>';  
	echo '            ,                                                                                                                              ', '<br>';
	echo '      .I7777~                                                                                                                              ', '<br>';
	echo '     .I7777777                                                                                                                             ', '<br>';
	echo '   +.  77777777                                                                                                                            ', '<br>';
	echo ' =???,  I7777777=                                                                                                                          ', '<br>';
	echo '=??????   7777777?   ,:::===?                                                                                                              ', '<br>';
	echo '=???????.  777777777777777777~         .77:    ??           :7                                              =$,     :$$$$$$+  =$?          ', '<br>';
	echo ' ????????: .777777777777777777         II77    ??           :7                                              $$7     :$?   7$7 =$?          ', '<br>';
	echo '  .???????=  +7777777777777777        .7 =7:   ??   :7777+  :7:I777?    ?777I=  77~777? ,777I I7      77   +$?$:    :$?    $$ =$?          ', '<br>';
	echo '    ???????+  ~777???+===:::         :7+  ~7   ?? .77    +7 :7?.   II  7~   ,I7 77+   I77   ~7 ?7    =7:  .$, =$    :$?  ,$$? =$?          ', '<br>';
	echo '    ,???????~                        77    7:  ?? ?I.     7 :7     :7 ~7      7 77    =7:    7  7    7~   7$   $=   :$$$$$$~  =$?          ', '<br>';
	echo '    .???????  ,???I77777777777~     :77777777~ ?? 7:        :7     :7 777777777:77    =7     7  +7  ~7   $$$$$$$$I  :$?       =$?          ', '<br>';
	echo '   .???????  ,7777777777777777      7=      77 ?? I+      7 :7     :7 ??      7,77    =7     7   7~ 7,  =$7     $$, :$?       =$?          ', '<br>';
	echo '  .???????. I77777777777777777     +7       ,7???  77    I7 :7     :7  7~   .?7 77    =7     7   ,77I   $+       7$ :$?       =$?          ', '<br>';
	echo ' ,???????= :77777777777777777~     7=        ~7??  ~I77777  :7     :7  ,777777. 77    =7     7    77,  +$        .$::$?       =$?          ', '<br>';
	echo ',???????  :7777777                                                                                77                                       ', '<br>';
	echo ' =?????  ,7777777                                                                               77=                                        ', '<br>';
	echo '   +?+  7777777?                                                                                                                           ', '<br>';
	echo '    +  ~7777777                                                                                                                            ', '<br>';
	echo '       I777777                                                                                                                             ', '<br>';
	echo '          :~                                                                                                                               ', '<br>';


	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#       Image Keyword Example              #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing Image URL: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->image_keywords('url', $demo_url, array('extractMode'=>'trust-metadata'));

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Image Keywords ##', '<br>';
		foreach ($response['imageKeywords'] as $imageKeywords) {
			echo 'image keyword: ', $imageKeywords['text'], '<br>';	
			echo 'score: ', $imageKeywords['score'], '<br>';		
			echo '<br>';
		}
	} else {
		echo 'Error in the image keyword extraction call: ', $response['statusInfo'];
	}
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	/*
	$imageName = "grumpy-cat-meme-hmmm.jpg";
	$imageFile = fopen($imageName, "r") or die("Unable to open file!");
	$imageData = fread($imageFile,filesize($imageName));
	fclose($imageFile);


	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#       Image Keyword Example with image   #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing Image File: ', $imageName, '<br>';
	echo '<br>';

	$response = $alchemyapi->image_keywords('image', $imageData, array('imagePostMode'=>'raw'));

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Image Keywords ##', '<br>';
		foreach ($response['imageKeywords'] as $imageKeywords) {
			echo 'image keyword: ', $imageKeywords['text'], '<br>';	
			echo 'score: ', $imageKeywords['score'], '<br>';		
			echo '<br>';
		}
	} else {
		echo 'Error in the image keyword extraction call: ', $response['statusInfo'];
	}
	echo '<br>';
	echo '<br>';*/
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Entity Extraction Example              #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->entities('text',$demo_text, array('sentiment'=>1));

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Entities ##', '<br>';
		foreach ($response['entities'] as $entity) {
			echo 'entity: ', $entity['text'], '<br>';
			echo 'type: ', $entity['type'], '<br>';
			echo 'relevance: ', $entity['relevance'], '<br>';
			echo 'sentiment: ', $entity['sentiment']['type']; 			
			if (array_key_exists('score', $entity['sentiment'])) {
				echo ' (' . $entity['sentiment']['score'] . ')', '<br>';
			} else {
				echo '<br>';
			}
			
			echo '<br>';
		}
	} else {
		echo 'Error in the entity extraction call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Keyword Extraction Example             #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->keywords('text',$demo_text, array('sentiment'=>1));

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Keywords ##', '<br>';
		foreach ($response['keywords'] as $keyword) {
			echo 'keyword: ', $keyword['text'], '<br>';
			echo 'relevance: ', $keyword['relevance'], '<br>';
			echo 'sentiment: ', $keyword['sentiment']['type']; 			
			if (array_key_exists('score', $keyword['sentiment'])) {
				echo ' (' . $keyword['sentiment']['score'] . ')', '<br>';
			} else {
				echo '<br>';
			}
			echo '<br>';
		}
	} else {
		echo 'Error in the keyword extraction call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Concept Tagging Example                 #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->concepts('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Concepts ##', '<br>';
		foreach ($response['concepts'] as $concept) {
			echo 'concept: ', $concept['text'], '<br>';
			echo 'relevance: ', $concept['relevance'], '<br>';
			echo '<br>';
		}
	} else {
		echo 'Error in the concept tagging call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Sentiment Analysis Example             #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing HTML: ', $demo_html, '<br>';
	echo '<br>';

	$response = $alchemyapi->sentiment('html',$demo_html, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Document Sentiment ##', '<br>';
		echo 'sentiment: ', $response['docSentiment']['type'], '<br>';
		if (array_key_exists('score', $response['docSentiment'])) {
			echo 'score: ', $response['docSentiment']['score'], '<br>';
		}
	} else {
		echo 'Error in the sentiment analysis call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Targeted Sentiment Analysis Example    #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo 'Target: Denver, Colorado', '<br>';
	echo '<br>';

	$response = $alchemyapi->sentiment_targeted('text',$demo_text,'Denver', null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Targeted Sentiment ##', '<br>';
		echo 'sentiment: ', $response['docSentiment']['type'], '<br>';
		if (array_key_exists('score', $response['docSentiment'])) {
			echo 'score: ', $response['docSentiment']['score'], '<br>';
		}
	} else {
		echo 'Error in the targeted sentiment analysis call: ', $response['statusInfo'];
	}
	

	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Text Extraction Example                #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->text('url', $demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Extracted Text ##', '<br>';
		echo 'text: ','<br>', $response['text'], '<br>';
	} else {
		echo 'Error in the text extraction call: ', $response['statusInfo'];
	}
	

	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Author Extraction Example              #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->author('url',$demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Author ##', '<br>';
		echo 'author: ', $response['author'], '<br>';
	} else {
		echo 'Error in the author extraction call: ', $response['statusInfo'];
	}

	
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Language Detection Example             #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->language('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Language ##', '<br>';
		echo 'language: ', $response['language'], '<br>';
		echo 'iso-639-1: ', $response['iso-639-1'], '<br>';
		echo 'native speakers: ', $response['native-speakers'], '<br>';
	} else {
		echo 'Error in the language detection call: ', $response['statusInfo'];
	}

	
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Title Extraction Example               #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->title('url',$demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Title ##', '<br>';
		echo 'title: ', $response['title'], '<br>';
	} else {
		echo 'Error in the title extraction call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Relation Extraction Example            #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->relations('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Relations ##', '<br>';
		foreach ($response['relations'] as $relation) {
			if (array_key_exists('subject', $relation)) {
				echo 'Subject: ', $relation['subject']['text'], '<br>';
			}

			if (array_key_exists('action', $relation)) {
				echo 'Action: ', $relation['action']['text'], '<br>';
			}

			if (array_key_exists('object', $relation)) {
				echo 'Object: ', $relation['object']['text'], '<br>';
			}
			echo '<br>';
		}
	} else {
		echo 'Error in the relation extraction call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Text Categorization Example            #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->category('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Category ##', '<br>';
		echo 'category: ', $response['category'], '<br>';
		echo 'score: ', $response['score'], '<br>';
	} else {
		echo 'Error in the text categorization call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Feed Detection Example                 #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->feeds('url',$demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Feeds ##', '<br>';
		foreach ($response['feeds'] as $feed) {
			echo 'feed: ', $feed['feed'], '<br>';
		}
	} else {
		echo 'Error in the feed detection call: ', $response['statusInfo'];
	}


	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Microformats Parsing Example           #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->microformats('url',$demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Microformats ##', '<br>';
		foreach ($response['microformats'] as $microformat) {
			echo 'field: ', $microformat['field'], '<br>';
			echo 'data: ', $microformat['data'], '<br>', '<br>';
		}
	} else {
		echo 'Error in the microformat parsing call: ', $response['statusInfo'];
	}
	
	
	echo '<br>';
	echo '<br>';

	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   Image Extraction Example               #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing url: ', $demo_url, '<br>';
	echo '<br>';

	$response = $alchemyapi->imageExtraction('url',$demo_url, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Image ##', '<br>';
		echo 'Image: ', $response['image'], '<br>';
	} else {
		echo 'Error in the image extraction call: ', $response['statusInfo'];
	}
	
	
	echo '<br>';
	echo '<br>';

	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   taxonomy Example                       #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->taxonomy('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		echo '## Categories ##', '<br>';
		foreach ($response['taxonomy'] as $category) {
		  echo $category['label'], ' : ', $category['score'], '<br>';
		}
	} else {
		echo 'Error in the taxonomy call: ', $response['statusInfo'];
	}
		
	echo '<br>';
	echo '<br>';

	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '############################################', '<br>';
	echo '#   combined Example                       #', '<br>';
	echo '############################################', '<br>';
	echo '<br>';
	echo '<br>';
	
	echo 'Processing text: ', $demo_text, '<br>';
	echo '<br>';

	$response = $alchemyapi->combined('text',$demo_text, null);

	if ($response['status'] == 'OK') {
		echo '## Response Object ##', '<br>';
		echo print_r($response);

		echo '<br>';
		
		echo '## Keywords ##', '<br>';
		foreach ($response['keywords'] as $keyword) {
		  echo $keyword['text'], ' : ', $keyword['relevance'], '<br>';
		}
		echo '<br>';
		
		echo '## Concepts ##', '<br>';
		foreach ($response['concepts'] as $concept) {
		  echo $concept['text'], ' : ', $concept['relevance'], '<br>';
		}
		echo '<br>';

		echo '## Entities ##', '<br>';
		foreach ($response['entities'] as $entity) {
		  echo $entity['type'], ' : ', $entity['text'], ' , ', $entity['relevance'], '<br>';
		}
		echo '<br>';
	} else {
		echo 'Error in the taxonomy call: ', $response['statusInfo'];
	}
		
	echo '<br>';
	echo '<br>';

?>
