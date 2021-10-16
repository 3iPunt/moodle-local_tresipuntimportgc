<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * Service definition for Forms (v1beta).
 *
 * <p>
 * Reads and writes Google Forms and responses.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/forms/api" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_Service_Forms extends Google_Service
{
  /** See, edit, create, and delete all of your Google Drive files. */
  const DRIVE =
      "https://www.googleapis.com/auth/drive";
  /** See and download all your Google Drive files. */
  const DRIVE_READONLY =
      "https://www.googleapis.com/auth/drive.readonly";

  public $forms;
  public $forms_responses;
  public $forms_watches;


  /**
   * Constructs the internal representation of the Forms service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client)
  {
    parent::__construct($client);
    $this->rootUrl = 'https://forms.googleapis.com/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1beta';
    $this->serviceName = 'forms';

    $this->forms = new Google_Service_Forms_Forms_Resource(
        $this,
        $this->serviceName,
        'forms',
        array(
          'methods' => array(
            'batchUpdate' => array(
              'path' => 'v1beta/forms/{formId}:batchUpdate',
              'httpMethod' => 'POST',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'create' => array(
              'path' => 'v1beta/forms',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'get' => array(
              'path' => 'v1beta/forms/{formId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->forms_responses = new Google_Service_Forms_FormsResponses_Resource(
        $this,
        $this->serviceName,
        'responses',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'v1beta/forms/{formId}/responses/{responseId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'responseId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'v1beta/forms/{formId}/responses',
              'httpMethod' => 'GET',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'filter' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'pageSize' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),
          )
        )
    );
    $this->forms_watches = new Google_Service_Forms_FormsWatches_Resource(
        $this,
        $this->serviceName,
        'watches',
        array(
          'methods' => array(
            'create' => array(
              'path' => 'v1beta/forms/{formId}/watches',
              'httpMethod' => 'POST',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'delete' => array(
              'path' => 'v1beta/forms/{formId}/watches/{watchId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'watchId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'v1beta/forms/{formId}/watches',
              'httpMethod' => 'GET',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'renew' => array(
              'path' => 'v1beta/forms/{formId}/watches/{watchId}:renew',
              'httpMethod' => 'POST',
              'parameters' => array(
                'formId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'watchId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
  }
}


/**
 * The "forms" collection of methods.
 * Typical usage is:
 *  <code>
 *   $formsService = new Google_Service_Forms(...);
 *   $forms = $formsService->forms;
 *  </code>
 */
class Google_Service_Forms_Forms_Resource extends Google_Service_Resource
{

  /**
   * Change the form with a batch of updates. (forms.batchUpdate)
   *
   * @param string $formId Required. The form ID.
   * @param Google_BatchUpdateFormRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_BatchUpdateFormResponse
   */
  public function batchUpdate($formId, Google_Service_Forms_BatchUpdateFormRequest $postBody, $optParams = array())
  {
    $params = array('formId' => $formId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('batchUpdate', array($params), "Google_Service_Forms_BatchUpdateFormResponse");
  }

  /**
   * Create a new form using the title given in the provided form message in the
   * request. *Important:* Only the form.info.title field is copied to the new
   * form. All other fields including the form description, items and settings are
   * disallowed. To create a new form and add items, you must first call
   * forms.create to create an empty form with a title then call forms.update to
   * add the items. (forms.create)
   *
   * @param Google_Form $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_Form
   */
  public function create(Google_Service_Forms_Form $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Google_Service_Forms_Form");
  }

  /**
   * Get a form. (forms.get)
   *
   * @param string $formId Required. The form ID.
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_Form
   */
  public function get($formId, $optParams = array())
  {
    $params = array('formId' => $formId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Google_Service_Forms_Form");
  }
}

/**
 * The "responses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $formsService = new Google_Service_Forms(...);
 *   $responses = $formsService->responses;
 *  </code>
 */
class Google_Service_Forms_FormsResponses_Resource extends Google_Service_Resource
{

  /**
   * Get one response from the form. (responses.get)
   *
   * @param string $formId Required. The form ID.
   * @param string $responseId Required. The response ID within the form.
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_FormResponse
   */
  public function get($formId, $responseId, $optParams = array())
  {
    $params = array('formId' => $formId, 'responseId' => $responseId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Google_Service_Forms_FormResponse");
  }

  /**
   * List a form's responses. (responses.listFormsResponses)
   *
   * @param string $formId Required. ID of the Form whose responses to list.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Which form responses to return. Currently, the only
   * supported filter is timestamp >= *N* (whitespace ignored) which means to get
   * all form responses submitted at and after timestamp *N*, which is an RFC-3339
   * formatted string (e.g. 2012-04-21T11:30:00-04:00). UTC offsets are supported.
   * @opt_param int pageSize The maximum number of responses to return. The
   * service may return fewer than this value. If unspecified or zero, at most
   * 5000 responses are returned.
   * @opt_param string pageToken A page token returned by a previous list
   * response. If this field is set, the form and the values of the filter must be
   * the same as for the original request.
   * @return Google_Service_Forms_ListFormResponsesResponse
   */
  public function listFormsResponses($formId, $optParams = array())
  {
    $params = array('formId' => $formId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_Forms_ListFormResponsesResponse");
  }
}
/**
 * The "watches" collection of methods.
 * Typical usage is:
 *  <code>
 *   $formsService = new Google_Service_Forms(...);
 *   $watches = $formsService->watches;
 *  </code>
 */
class Google_Service_Forms_FormsWatches_Resource extends Google_Service_Resource
{

  /**
   * Create a new watch. If a watch ID is provided, it must be unused. For each
   * invoking project, the per form limit is one watch per Watch.EventType. A
   * watch expires seven days after it is created (see Watch.expire_time).
   * (watches.create)
   *
   * @param string $formId Required. ID of the Form to watch.
   * @param Google_CreateWatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_Watch
   */
  public function create($formId, Google_Service_Forms_CreateWatchRequest $postBody, $optParams = array())
  {
    $params = array('formId' => $formId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Google_Service_Forms_Watch");
  }

  /**
   * Delete a watch. (watches.delete)
   *
   * @param string $formId Required. The ID of the Form.
   * @param string $watchId Required. The ID of the Watch to delete.
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_FormsEmpty
   */
  public function delete($formId, $watchId, $optParams = array())
  {
    $params = array('formId' => $formId, 'watchId' => $watchId);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params), "Google_Service_Forms_FormsEmpty");
  }

  /**
   * Return a list of the watches owned by the invoking project. The maximum
   * number of watches is two: For each invoker, the limit is one for each event
   * type per form. (watches.listFormsWatches)
   *
   * @param string $formId Required. ID of the Form whose watches to list.
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_ListWatchesResponse
   */
  public function listFormsWatches($formId, $optParams = array())
  {
    $params = array('formId' => $formId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_Forms_ListWatchesResponse");
  }

  /**
   * Renew an existing watch for seven days. The state of the watch after renewal
   * is `ACTIVE`, and the `expire_time` is seven days from the renewal. Renewing a
   * watch in an error state (e.g. `SUSPENDED`) succeeds if the error is no longer
   * present, but fail otherwise. After a watch has expired, RenewWatch returns
   * `NOT_FOUND`. (watches.renew)
   *
   * @param string $formId Required. The ID of the Form.
   * @param string $watchId Required. The ID of the Watch to renew.
   * @param Google_RenewWatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Forms_Watch
   */
  public function renew($formId, $watchId, Google_Service_Forms_RenewWatchRequest $postBody, $optParams = array())
  {
    $params = array('formId' => $formId, 'watchId' => $watchId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('renew', array($params), "Google_Service_Forms_Watch");
  }
}




class Google_Service_Forms_Answer extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $fileUploadAnswersType = 'Google_Service_Forms_FileUploadAnswers';
  protected $fileUploadAnswersDataType = '';
  protected $gradeType = 'Google_Service_Forms_Grade';
  protected $gradeDataType = '';
  public $questionId;
  protected $textAnswersType = 'Google_Service_Forms_TextAnswers';
  protected $textAnswersDataType = '';


  public function setFileUploadAnswers(Google_Service_Forms_FileUploadAnswers $fileUploadAnswers)
  {
    $this->fileUploadAnswers = $fileUploadAnswers;
  }
  public function getFileUploadAnswers()
  {
    return $this->fileUploadAnswers;
  }
  public function setGrade(Google_Service_Forms_Grade $grade)
  {
    $this->grade = $grade;
  }
  public function getGrade()
  {
    return $this->grade;
  }
  public function setQuestionId($questionId)
  {
    $this->questionId = $questionId;
  }
  public function getQuestionId()
  {
    return $this->questionId;
  }
  public function setTextAnswers(Google_Service_Forms_TextAnswers $textAnswers)
  {
    $this->textAnswers = $textAnswers;
  }
  public function getTextAnswers()
  {
    return $this->textAnswers;
  }
}

class Google_Service_Forms_BatchUpdateFormRequest extends Google_Collection
{
  protected $collection_key = 'requests';
  protected $internal_gapi_mappings = array(
  );
  public $includeFormInResponse;
  protected $requestsType = 'Google_Service_Forms_Request';
  protected $requestsDataType = 'array';
  protected $writeControlType = 'Google_Service_Forms_WriteControl';
  protected $writeControlDataType = '';


  public function setIncludeFormInResponse($includeFormInResponse)
  {
    $this->includeFormInResponse = $includeFormInResponse;
  }
  public function getIncludeFormInResponse()
  {
    return $this->includeFormInResponse;
  }
  public function setRequests($requests)
  {
    $this->requests = $requests;
  }
  public function getRequests()
  {
    return $this->requests;
  }
  public function setWriteControl(Google_Service_Forms_WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

class Google_Service_Forms_BatchUpdateFormResponse extends Google_Collection
{
  protected $collection_key = 'replies';
  protected $internal_gapi_mappings = array(
  );
  protected $formType = 'Google_Service_Forms_Form';
  protected $formDataType = '';
  protected $repliesType = 'Google_Service_Forms_Response';
  protected $repliesDataType = 'array';
  protected $writeControlType = 'Google_Service_Forms_WriteControl';
  protected $writeControlDataType = '';


  public function setForm(Google_Service_Forms_Form $form)
  {
    $this->form = $form;
  }
  public function getForm()
  {
    return $this->form;
  }
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  public function getReplies()
  {
    return $this->replies;
  }
  public function setWriteControl(Google_Service_Forms_WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

class Google_Service_Forms_ChoiceQuestion extends Google_Collection
{
  protected $collection_key = 'options';
  protected $internal_gapi_mappings = array(
  );
  protected $optionsType = 'Google_Service_Forms_Option';
  protected $optionsDataType = 'array';
  public $shuffle;
  public $type;


  public function setOptions($options)
  {
    $this->options = $options;
  }
  public function getOptions()
  {
    return $this->options;
  }
  public function setShuffle($shuffle)
  {
    $this->shuffle = $shuffle;
  }
  public function getShuffle()
  {
    return $this->shuffle;
  }
  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
  }
}

class Google_Service_Forms_CloudPubsubTopic extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $topicName;


  public function setTopicName($topicName)
  {
    $this->topicName = $topicName;
  }
  public function getTopicName()
  {
    return $this->topicName;
  }
}

class Google_Service_Forms_CorrectAnswer extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $value;


  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Google_Service_Forms_CorrectAnswers extends Google_Collection
{
  protected $collection_key = 'answers';
  protected $internal_gapi_mappings = array(
  );
  protected $answersType = 'Google_Service_Forms_CorrectAnswer';
  protected $answersDataType = 'array';


  public function setAnswers($answers)
  {
    $this->answers = $answers;
  }
  public function getAnswers()
  {
    return $this->answers;
  }
}

class Google_Service_Forms_CreateItemRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $itemType = 'Google_Service_Forms_Item';
  protected $itemDataType = '';
  protected $locationType = 'Google_Service_Forms_Location';
  protected $locationDataType = '';


  public function setItem(Google_Service_Forms_Item $item)
  {
    $this->item = $item;
  }
  public function getItem()
  {
    return $this->item;
  }
  public function setLocation(Google_Service_Forms_Location $location)
  {
    $this->location = $location;
  }
  public function getLocation()
  {
    return $this->location;
  }
}

class Google_Service_Forms_CreateItemResponse extends Google_Collection
{
  protected $collection_key = 'questionId';
  protected $internal_gapi_mappings = array(
  );
  public $itemId;
  public $questionId;


  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  public function getItemId()
  {
    return $this->itemId;
  }
  public function setQuestionId($questionId)
  {
    $this->questionId = $questionId;
  }
  public function getQuestionId()
  {
    return $this->questionId;
  }
}

class Google_Service_Forms_CreateWatchRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $watchType = 'Google_Service_Forms_Watch';
  protected $watchDataType = '';
  public $watchId;


  public function setWatch(Google_Service_Forms_Watch $watch)
  {
    $this->watch = $watch;
  }
  public function getWatch()
  {
    return $this->watch;
  }
  public function setWatchId($watchId)
  {
    $this->watchId = $watchId;
  }
  public function getWatchId()
  {
    return $this->watchId;
  }
}

class Google_Service_Forms_DateQuestion extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $includeTime;
  public $includeYear;


  public function setIncludeTime($includeTime)
  {
    $this->includeTime = $includeTime;
  }
  public function getIncludeTime()
  {
    return $this->includeTime;
  }
  public function setIncludeYear($includeYear)
  {
    $this->includeYear = $includeYear;
  }
  public function getIncludeYear()
  {
    return $this->includeYear;
  }
}

class Google_Service_Forms_DeleteItemRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $locationType = 'Google_Service_Forms_Location';
  protected $locationDataType = '';


  public function setLocation(Google_Service_Forms_Location $location)
  {
    $this->location = $location;
  }
  public function getLocation()
  {
    return $this->location;
  }
}

class Google_Service_Forms_ExtraMaterial extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $linkType = 'Google_Service_Forms_TextLink';
  protected $linkDataType = '';
  protected $videoType = 'Google_Service_Forms_VideoLink';
  protected $videoDataType = '';


  public function setLink(Google_Service_Forms_TextLink $link)
  {
    $this->link = $link;
  }
  public function getLink()
  {
    return $this->link;
  }
  public function setVideo(Google_Service_Forms_VideoLink $video)
  {
    $this->video = $video;
  }
  public function getVideo()
  {
    return $this->video;
  }
}

class Google_Service_Forms_Feedback extends Google_Collection
{
  protected $collection_key = 'material';
  protected $internal_gapi_mappings = array(
  );
  protected $materialType = 'Google_Service_Forms_ExtraMaterial';
  protected $materialDataType = 'array';
  public $text;


  public function setMaterial($material)
  {
    $this->material = $material;
  }
  public function getMaterial()
  {
    return $this->material;
  }
  public function setText($text)
  {
    $this->text = $text;
  }
  public function getText()
  {
    return $this->text;
  }
}

class Google_Service_Forms_FileUploadAnswer extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $fileId;
  public $fileName;
  public $mimeType;


  public function setFileId($fileId)
  {
    $this->fileId = $fileId;
  }
  public function getFileId()
  {
    return $this->fileId;
  }
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  public function getFileName()
  {
    return $this->fileName;
  }
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

class Google_Service_Forms_FileUploadAnswers extends Google_Collection
{
  protected $collection_key = 'answers';
  protected $internal_gapi_mappings = array(
  );
  protected $answersType = 'Google_Service_Forms_FileUploadAnswer';
  protected $answersDataType = 'array';


  public function setAnswers($answers)
  {
    $this->answers = $answers;
  }
  public function getAnswers()
  {
    return $this->answers;
  }
}

class Google_Service_Forms_FileUploadQuestion extends Google_Collection
{
  protected $collection_key = 'types';
  protected $internal_gapi_mappings = array(
  );
  public $folderId;
  public $maxFileSize;
  public $maxFiles;
  public $types;


  public function setFolderId($folderId)
  {
    $this->folderId = $folderId;
  }
  public function getFolderId()
  {
    return $this->folderId;
  }
  public function setMaxFileSize($maxFileSize)
  {
    $this->maxFileSize = $maxFileSize;
  }
  public function getMaxFileSize()
  {
    return $this->maxFileSize;
  }
  public function setMaxFiles($maxFiles)
  {
    $this->maxFiles = $maxFiles;
  }
  public function getMaxFiles()
  {
    return $this->maxFiles;
  }
  public function setTypes($types)
  {
    $this->types = $types;
  }
  public function getTypes()
  {
    return $this->types;
  }
}

class Google_Service_Forms_Form extends Google_Collection
{
  protected $collection_key = 'items';
  protected $internal_gapi_mappings = array(
  );
  public $formId;
  protected $infoType = 'Google_Service_Forms_Info';
  protected $infoDataType = '';
  protected $itemsType = 'Google_Service_Forms_Item';
  protected $itemsDataType = 'array';
  public $responderUri;
  public $revisionId;
  protected $settingsType = 'Google_Service_Forms_FormSettings';
  protected $settingsDataType = '';


  public function setFormId($formId)
  {
    $this->formId = $formId;
  }
  public function getFormId()
  {
    return $this->formId;
  }
  public function setInfo(Google_Service_Forms_Info $info)
  {
    $this->info = $info;
  }
  public function getInfo()
  {
    return $this->info;
  }
  public function setItems($items)
  {
    $this->items = $items;
  }
  public function getItems()
  {
    return $this->items;
  }
  public function setResponderUri($responderUri)
  {
    $this->responderUri = $responderUri;
  }
  public function getResponderUri()
  {
    return $this->responderUri;
  }
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  public function setSettings(Google_Service_Forms_FormSettings $settings)
  {
    $this->settings = $settings;
  }
  public function getSettings()
  {
    return $this->settings;
  }
}

class Google_Service_Forms_FormResponse extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $answersType = 'Google_Service_Forms_Answer';
  protected $answersDataType = 'map';
  public $createTime;
  public $formId;
  public $lastSubmittedTime;
  public $respondentEmail;
  public $responseId;
  public $totalScore;


  public function setAnswers($answers)
  {
    $this->answers = $answers;
  }
  public function getAnswers()
  {
    return $this->answers;
  }
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  public function getCreateTime()
  {
    return $this->createTime;
  }
  public function setFormId($formId)
  {
    $this->formId = $formId;
  }
  public function getFormId()
  {
    return $this->formId;
  }
  public function setLastSubmittedTime($lastSubmittedTime)
  {
    $this->lastSubmittedTime = $lastSubmittedTime;
  }
  public function getLastSubmittedTime()
  {
    return $this->lastSubmittedTime;
  }
  public function setRespondentEmail($respondentEmail)
  {
    $this->respondentEmail = $respondentEmail;
  }
  public function getRespondentEmail()
  {
    return $this->respondentEmail;
  }
  public function setResponseId($responseId)
  {
    $this->responseId = $responseId;
  }
  public function getResponseId()
  {
    return $this->responseId;
  }
  public function setTotalScore($totalScore)
  {
    $this->totalScore = $totalScore;
  }
  public function getTotalScore()
  {
    return $this->totalScore;
  }
}

class Google_Service_Forms_FormSettings extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $quizSettingsType = 'Google_Service_Forms_QuizSettings';
  protected $quizSettingsDataType = '';


  public function setQuizSettings(Google_Service_Forms_QuizSettings $quizSettings)
  {
    $this->quizSettings = $quizSettings;
  }
  public function getQuizSettings()
  {
    return $this->quizSettings;
  }
}

class Google_Service_Forms_FormsEmpty extends Google_Model
{
}

class Google_Service_Forms_Grade extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $auto;
  public $correct;
  protected $feedbackType = 'Google_Service_Forms_Feedback';
  protected $feedbackDataType = '';
  public $score;


  public function setAuto($auto)
  {
    $this->auto = $auto;
  }
  public function getAuto()
  {
    return $this->auto;
  }
  public function setCorrect($correct)
  {
    $this->correct = $correct;
  }
  public function getCorrect()
  {
    return $this->correct;
  }
  public function setFeedback(Google_Service_Forms_Feedback $feedback)
  {
    $this->feedback = $feedback;
  }
  public function getFeedback()
  {
    return $this->feedback;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
}

class Google_Service_Forms_Grading extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $correctAnswersType = 'Google_Service_Forms_CorrectAnswers';
  protected $correctAnswersDataType = '';
  protected $generalFeedbackType = 'Google_Service_Forms_Feedback';
  protected $generalFeedbackDataType = '';
  public $pointValue;
  protected $whenRightType = 'Google_Service_Forms_Feedback';
  protected $whenRightDataType = '';
  protected $whenWrongType = 'Google_Service_Forms_Feedback';
  protected $whenWrongDataType = '';


  public function setCorrectAnswers(Google_Service_Forms_CorrectAnswers $correctAnswers)
  {
    $this->correctAnswers = $correctAnswers;
  }
  public function getCorrectAnswers()
  {
    return $this->correctAnswers;
  }
  public function setGeneralFeedback(Google_Service_Forms_Feedback $generalFeedback)
  {
    $this->generalFeedback = $generalFeedback;
  }
  public function getGeneralFeedback()
  {
    return $this->generalFeedback;
  }
  public function setPointValue($pointValue)
  {
    $this->pointValue = $pointValue;
  }
  public function getPointValue()
  {
    return $this->pointValue;
  }
  public function setWhenRight(Google_Service_Forms_Feedback $whenRight)
  {
    $this->whenRight = $whenRight;
  }
  public function getWhenRight()
  {
    return $this->whenRight;
  }
  public function setWhenWrong(Google_Service_Forms_Feedback $whenWrong)
  {
    $this->whenWrong = $whenWrong;
  }
  public function getWhenWrong()
  {
    return $this->whenWrong;
  }
}

class Google_Service_Forms_Grid extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $columnsType = 'Google_Service_Forms_ChoiceQuestion';
  protected $columnsDataType = '';
  public $shuffleQuestions;


  public function setColumns(Google_Service_Forms_ChoiceQuestion $columns)
  {
    $this->columns = $columns;
  }
  public function getColumns()
  {
    return $this->columns;
  }
  public function setShuffleQuestions($shuffleQuestions)
  {
    $this->shuffleQuestions = $shuffleQuestions;
  }
  public function getShuffleQuestions()
  {
    return $this->shuffleQuestions;
  }
}

class Google_Service_Forms_Image extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $altText;
  public $contentUri;
  protected $propertiesType = 'Google_Service_Forms_MediaProperties';
  protected $propertiesDataType = '';
  public $sourceUri;


  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  public function getAltText()
  {
    return $this->altText;
  }
  public function setContentUri($contentUri)
  {
    $this->contentUri = $contentUri;
  }
  public function getContentUri()
  {
    return $this->contentUri;
  }
  public function setProperties(Google_Service_Forms_MediaProperties $properties)
  {
    $this->properties = $properties;
  }
  public function getProperties()
  {
    return $this->properties;
  }
  public function setSourceUri($sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
}

class Google_Service_Forms_ImageItem extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $imageType = 'Google_Service_Forms_Image';
  protected $imageDataType = '';


  public function setImage(Google_Service_Forms_Image $image)
  {
    $this->image = $image;
  }
  public function getImage()
  {
    return $this->image;
  }
}

class Google_Service_Forms_Info extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $description;
  public $documentTitle;
  public $title;


  public function setDescription($description)
  {
    $this->description = $description;
  }
  public function getDescription()
  {
    return $this->description;
  }
  public function setDocumentTitle($documentTitle)
  {
    $this->documentTitle = $documentTitle;
  }
  public function getDocumentTitle()
  {
    return $this->documentTitle;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
}

class Google_Service_Forms_Item extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $description;
  protected $imageItemType = 'Google_Service_Forms_ImageItem';
  protected $imageItemDataType = '';
  public $itemId;
  protected $pageBreakItemType = 'Google_Service_Forms_PageBreakItem';
  protected $pageBreakItemDataType = '';
  protected $questionGroupItemType = 'Google_Service_Forms_QuestionGroupItem';
  protected $questionGroupItemDataType = '';
  protected $questionItemType = 'Google_Service_Forms_QuestionItem';
  protected $questionItemDataType = '';
  protected $textItemType = 'Google_Service_Forms_TextItem';
  protected $textItemDataType = '';
  public $title;
  protected $videoItemType = 'Google_Service_Forms_VideoItem';
  protected $videoItemDataType = '';


  public function setDescription($description)
  {
    $this->description = $description;
  }
  public function getDescription()
  {
    return $this->description;
  }
  public function setImageItem(Google_Service_Forms_ImageItem $imageItem)
  {
    $this->imageItem = $imageItem;
  }
  public function getImageItem()
  {
    return $this->imageItem;
  }
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  public function getItemId()
  {
    return $this->itemId;
  }
  public function setPageBreakItem(Google_Service_Forms_PageBreakItem $pageBreakItem)
  {
    $this->pageBreakItem = $pageBreakItem;
  }
  public function getPageBreakItem()
  {
    return $this->pageBreakItem;
  }
  public function setQuestionGroupItem(Google_Service_Forms_QuestionGroupItem $questionGroupItem)
  {
    $this->questionGroupItem = $questionGroupItem;
  }
  public function getQuestionGroupItem()
  {
    return $this->questionGroupItem;
  }
  public function setQuestionItem(Google_Service_Forms_QuestionItem $questionItem)
  {
    $this->questionItem = $questionItem;
  }
  public function getQuestionItem()
  {
    return $this->questionItem;
  }
  public function setTextItem(Google_Service_Forms_TextItem $textItem)
  {
    $this->textItem = $textItem;
  }
  public function getTextItem()
  {
    return $this->textItem;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
  public function setVideoItem(Google_Service_Forms_VideoItem $videoItem)
  {
    $this->videoItem = $videoItem;
  }
  public function getVideoItem()
  {
    return $this->videoItem;
  }
}

class Google_Service_Forms_ListFormResponsesResponse extends Google_Collection
{
  protected $collection_key = 'responses';
  protected $internal_gapi_mappings = array(
  );
  public $nextPageToken;
  protected $responsesType = 'Google_Service_Forms_FormResponse';
  protected $responsesDataType = 'array';


  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResponses($responses)
  {
    $this->responses = $responses;
  }
  public function getResponses()
  {
    return $this->responses;
  }
}

class Google_Service_Forms_ListWatchesResponse extends Google_Collection
{
  protected $collection_key = 'watches';
  protected $internal_gapi_mappings = array(
  );
  protected $watchesType = 'Google_Service_Forms_Watch';
  protected $watchesDataType = 'array';


  public function setWatches($watches)
  {
    $this->watches = $watches;
  }
  public function getWatches()
  {
    return $this->watches;
  }
}

class Google_Service_Forms_Location extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $index;


  public function setIndex($index)
  {
    $this->index = $index;
  }
  public function getIndex()
  {
    return $this->index;
  }
}

class Google_Service_Forms_MediaProperties extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $alignment;


  public function setAlignment($alignment)
  {
    $this->alignment = $alignment;
  }
  public function getAlignment()
  {
    return $this->alignment;
  }
}

class Google_Service_Forms_MoveItemRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $newLocationType = 'Google_Service_Forms_Location';
  protected $newLocationDataType = '';
  protected $originalLocationType = 'Google_Service_Forms_Location';
  protected $originalLocationDataType = '';


  public function setNewLocation(Google_Service_Forms_Location $newLocation)
  {
    $this->newLocation = $newLocation;
  }
  public function getNewLocation()
  {
    return $this->newLocation;
  }
  public function setOriginalLocation(Google_Service_Forms_Location $originalLocation)
  {
    $this->originalLocation = $originalLocation;
  }
  public function getOriginalLocation()
  {
    return $this->originalLocation;
  }
}

class Google_Service_Forms_Option extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $goToAction;
  public $goToSectionId;
  protected $imageType = 'Google_Service_Forms_Image';
  protected $imageDataType = '';
  public $isOther;
  public $value;


  public function setGoToAction($goToAction)
  {
    $this->goToAction = $goToAction;
  }
  public function getGoToAction()
  {
    return $this->goToAction;
  }
  public function setGoToSectionId($goToSectionId)
  {
    $this->goToSectionId = $goToSectionId;
  }
  public function getGoToSectionId()
  {
    return $this->goToSectionId;
  }
  public function setImage(Google_Service_Forms_Image $image)
  {
    $this->image = $image;
  }
  public function getImage()
  {
    return $this->image;
  }
  public function setIsOther($isOther)
  {
    $this->isOther = $isOther;
  }
  public function getIsOther()
  {
    return $this->isOther;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Google_Service_Forms_PageBreakItem extends Google_Model
{
}

class Google_Service_Forms_Question extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $choiceQuestionType = 'Google_Service_Forms_ChoiceQuestion';
  protected $choiceQuestionDataType = '';
  protected $dateQuestionType = 'Google_Service_Forms_DateQuestion';
  protected $dateQuestionDataType = '';
  protected $fileUploadQuestionType = 'Google_Service_Forms_FileUploadQuestion';
  protected $fileUploadQuestionDataType = '';
  protected $gradingType = 'Google_Service_Forms_Grading';
  protected $gradingDataType = '';
  public $questionId;
  public $required;
  protected $rowQuestionType = 'Google_Service_Forms_RowQuestion';
  protected $rowQuestionDataType = '';
  protected $scaleQuestionType = 'Google_Service_Forms_ScaleQuestion';
  protected $scaleQuestionDataType = '';
  protected $textQuestionType = 'Google_Service_Forms_TextQuestion';
  protected $textQuestionDataType = '';
  protected $timeQuestionType = 'Google_Service_Forms_TimeQuestion';
  protected $timeQuestionDataType = '';


  public function setChoiceQuestion(Google_Service_Forms_ChoiceQuestion $choiceQuestion)
  {
    $this->choiceQuestion = $choiceQuestion;
  }
  public function getChoiceQuestion()
  {
    return $this->choiceQuestion;
  }
  public function setDateQuestion(Google_Service_Forms_DateQuestion $dateQuestion)
  {
    $this->dateQuestion = $dateQuestion;
  }
  public function getDateQuestion()
  {
    return $this->dateQuestion;
  }
  public function setFileUploadQuestion(Google_Service_Forms_FileUploadQuestion $fileUploadQuestion)
  {
    $this->fileUploadQuestion = $fileUploadQuestion;
  }
  public function getFileUploadQuestion()
  {
    return $this->fileUploadQuestion;
  }
  public function setGrading(Google_Service_Forms_Grading $grading)
  {
    $this->grading = $grading;
  }
  public function getGrading()
  {
    return $this->grading;
  }
  public function setQuestionId($questionId)
  {
    $this->questionId = $questionId;
  }
  public function getQuestionId()
  {
    return $this->questionId;
  }
  public function setRequired($required)
  {
    $this->required = $required;
  }
  public function getRequired()
  {
    return $this->required;
  }
  public function setRowQuestion(Google_Service_Forms_RowQuestion $rowQuestion)
  {
    $this->rowQuestion = $rowQuestion;
  }
  public function getRowQuestion()
  {
    return $this->rowQuestion;
  }
  public function setScaleQuestion(Google_Service_Forms_ScaleQuestion $scaleQuestion)
  {
    $this->scaleQuestion = $scaleQuestion;
  }
  public function getScaleQuestion()
  {
    return $this->scaleQuestion;
  }
  public function setTextQuestion(Google_Service_Forms_TextQuestion $textQuestion)
  {
    $this->textQuestion = $textQuestion;
  }
  public function getTextQuestion()
  {
    return $this->textQuestion;
  }
  public function setTimeQuestion(Google_Service_Forms_TimeQuestion $timeQuestion)
  {
    $this->timeQuestion = $timeQuestion;
  }
  public function getTimeQuestion()
  {
    return $this->timeQuestion;
  }
}

class Google_Service_Forms_QuestionGroupItem extends Google_Collection
{
  protected $collection_key = 'questions';
  protected $internal_gapi_mappings = array(
  );
  protected $gridType = 'Google_Service_Forms_Grid';
  protected $gridDataType = '';
  protected $imageType = 'Google_Service_Forms_Image';
  protected $imageDataType = '';
  protected $questionsType = 'Google_Service_Forms_Question';
  protected $questionsDataType = 'array';


  public function setGrid(Google_Service_Forms_Grid $grid)
  {
    $this->grid = $grid;
  }
  public function getGrid()
  {
    return $this->grid;
  }
  public function setImage(Google_Service_Forms_Image $image)
  {
    $this->image = $image;
  }
  public function getImage()
  {
    return $this->image;
  }
  public function setQuestions($questions)
  {
    $this->questions = $questions;
  }
  public function getQuestions()
  {
    return $this->questions;
  }
}

class Google_Service_Forms_QuestionItem extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $imageType = 'Google_Service_Forms_Image';
  protected $imageDataType = '';
  protected $questionType = 'Google_Service_Forms_Question';
  protected $questionDataType = '';


  public function setImage(Google_Service_Forms_Image $image)
  {
    $this->image = $image;
  }
  public function getImage()
  {
    return $this->image;
  }
  public function setQuestion(Google_Service_Forms_Question $question)
  {
    $this->question = $question;
  }
  public function getQuestion()
  {
    return $this->question;
  }
}

class Google_Service_Forms_QuizSettings extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $isQuiz;


  public function setIsQuiz($isQuiz)
  {
    $this->isQuiz = $isQuiz;
  }
  public function getIsQuiz()
  {
    return $this->isQuiz;
  }
}

class Google_Service_Forms_RenewWatchRequest extends Google_Model
{
}

class Google_Service_Forms_Request extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $createItemType = 'Google_Service_Forms_CreateItemRequest';
  protected $createItemDataType = '';
  protected $deleteItemType = 'Google_Service_Forms_DeleteItemRequest';
  protected $deleteItemDataType = '';
  protected $moveItemType = 'Google_Service_Forms_MoveItemRequest';
  protected $moveItemDataType = '';
  protected $updateFormInfoType = 'Google_Service_Forms_UpdateFormInfoRequest';
  protected $updateFormInfoDataType = '';
  protected $updateItemType = 'Google_Service_Forms_UpdateItemRequest';
  protected $updateItemDataType = '';
  protected $updateSettingsType = 'Google_Service_Forms_UpdateSettingsRequest';
  protected $updateSettingsDataType = '';


  public function setCreateItem(Google_Service_Forms_CreateItemRequest $createItem)
  {
    $this->createItem = $createItem;
  }
  public function getCreateItem()
  {
    return $this->createItem;
  }
  public function setDeleteItem(Google_Service_Forms_DeleteItemRequest $deleteItem)
  {
    $this->deleteItem = $deleteItem;
  }
  public function getDeleteItem()
  {
    return $this->deleteItem;
  }
  public function setMoveItem(Google_Service_Forms_MoveItemRequest $moveItem)
  {
    $this->moveItem = $moveItem;
  }
  public function getMoveItem()
  {
    return $this->moveItem;
  }
  public function setUpdateFormInfo(Google_Service_Forms_UpdateFormInfoRequest $updateFormInfo)
  {
    $this->updateFormInfo = $updateFormInfo;
  }
  public function getUpdateFormInfo()
  {
    return $this->updateFormInfo;
  }
  public function setUpdateItem(Google_Service_Forms_UpdateItemRequest $updateItem)
  {
    $this->updateItem = $updateItem;
  }
  public function getUpdateItem()
  {
    return $this->updateItem;
  }
  public function setUpdateSettings(Google_Service_Forms_UpdateSettingsRequest $updateSettings)
  {
    $this->updateSettings = $updateSettings;
  }
  public function getUpdateSettings()
  {
    return $this->updateSettings;
  }
}

class Google_Service_Forms_Response extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $createItemType = 'Google_Service_Forms_CreateItemResponse';
  protected $createItemDataType = '';


  public function setCreateItem(Google_Service_Forms_CreateItemResponse $createItem)
  {
    $this->createItem = $createItem;
  }
  public function getCreateItem()
  {
    return $this->createItem;
  }
}

class Google_Service_Forms_RowQuestion extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $title;


  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
}

class Google_Service_Forms_ScaleQuestion extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $high;
  public $highLabel;
  public $low;
  public $lowLabel;


  public function setHigh($high)
  {
    $this->high = $high;
  }
  public function getHigh()
  {
    return $this->high;
  }
  public function setHighLabel($highLabel)
  {
    $this->highLabel = $highLabel;
  }
  public function getHighLabel()
  {
    return $this->highLabel;
  }
  public function setLow($low)
  {
    $this->low = $low;
  }
  public function getLow()
  {
    return $this->low;
  }
  public function setLowLabel($lowLabel)
  {
    $this->lowLabel = $lowLabel;
  }
  public function getLowLabel()
  {
    return $this->lowLabel;
  }
}

class Google_Service_Forms_TextAnswer extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $value;


  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Google_Service_Forms_TextAnswers extends Google_Collection
{
  protected $collection_key = 'answers';
  protected $internal_gapi_mappings = array(
  );
  protected $answersType = 'Google_Service_Forms_TextAnswer';
  protected $answersDataType = 'array';


  public function setAnswers($answers)
  {
    $this->answers = $answers;
  }
  public function getAnswers()
  {
    return $this->answers;
  }
}

class Google_Service_Forms_TextItem extends Google_Model
{
}

class Google_Service_Forms_TextLink extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $displayText;
  public $uri;


  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  public function getDisplayText()
  {
    return $this->displayText;
  }
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  public function getUri()
  {
    return $this->uri;
  }
}

class Google_Service_Forms_TextQuestion extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $paragraph;


  public function setParagraph($paragraph)
  {
    $this->paragraph = $paragraph;
  }
  public function getParagraph()
  {
    return $this->paragraph;
  }
}

class Google_Service_Forms_TimeQuestion extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $duration;


  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  public function getDuration()
  {
    return $this->duration;
  }
}

class Google_Service_Forms_UpdateFormInfoRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $infoType = 'Google_Service_Forms_Info';
  protected $infoDataType = '';
  public $updateMask;


  public function setInfo(Google_Service_Forms_Info $info)
  {
    $this->info = $info;
  }
  public function getInfo()
  {
    return $this->info;
  }
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

class Google_Service_Forms_UpdateItemRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $itemType = 'Google_Service_Forms_Item';
  protected $itemDataType = '';
  protected $locationType = 'Google_Service_Forms_Location';
  protected $locationDataType = '';
  public $updateMask;


  public function setItem(Google_Service_Forms_Item $item)
  {
    $this->item = $item;
  }
  public function getItem()
  {
    return $this->item;
  }
  public function setLocation(Google_Service_Forms_Location $location)
  {
    $this->location = $location;
  }
  public function getLocation()
  {
    return $this->location;
  }
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

class Google_Service_Forms_UpdateSettingsRequest extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $settingsType = 'Google_Service_Forms_FormSettings';
  protected $settingsDataType = '';
  public $updateMask;


  public function setSettings(Google_Service_Forms_FormSettings $settings)
  {
    $this->settings = $settings;
  }
  public function getSettings()
  {
    return $this->settings;
  }
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

class Google_Service_Forms_Video extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $propertiesType = 'Google_Service_Forms_MediaProperties';
  protected $propertiesDataType = '';
  public $youtubeUri;


  public function setProperties(Google_Service_Forms_MediaProperties $properties)
  {
    $this->properties = $properties;
  }
  public function getProperties()
  {
    return $this->properties;
  }
  public function setYoutubeUri($youtubeUri)
  {
    $this->youtubeUri = $youtubeUri;
  }
  public function getYoutubeUri()
  {
    return $this->youtubeUri;
  }
}

class Google_Service_Forms_VideoItem extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $caption;
  protected $videoType = 'Google_Service_Forms_Video';
  protected $videoDataType = '';


  public function setCaption($caption)
  {
    $this->caption = $caption;
  }
  public function getCaption()
  {
    return $this->caption;
  }
  public function setVideo(Google_Service_Forms_Video $video)
  {
    $this->video = $video;
  }
  public function getVideo()
  {
    return $this->video;
  }
}

class Google_Service_Forms_VideoLink extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $displayText;
  public $youtubeUri;


  public function setDisplayText($displayText)
  {
    $this->displayText = $displayText;
  }
  public function getDisplayText()
  {
    return $this->displayText;
  }
  public function setYoutubeUri($youtubeUri)
  {
    $this->youtubeUri = $youtubeUri;
  }
  public function getYoutubeUri()
  {
    return $this->youtubeUri;
  }
}

class Google_Service_Forms_Watch extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $createTime;
  public $errorType;
  public $eventType;
  public $expireTime;
  public $handback;
  public $id;
  public $state;
  protected $targetType = 'Google_Service_Forms_WatchTarget';
  protected $targetDataType = '';


  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  public function getCreateTime()
  {
    return $this->createTime;
  }
  public function setErrorType($errorType)
  {
    $this->errorType = $errorType;
  }
  public function getErrorType()
  {
    return $this->errorType;
  }
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  public function getEventType()
  {
    return $this->eventType;
  }
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  public function setHandback($handback)
  {
    $this->handback = $handback;
  }
  public function getHandback()
  {
    return $this->handback;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setState($state)
  {
    $this->state = $state;
  }
  public function getState()
  {
    return $this->state;
  }
  public function setTarget(Google_Service_Forms_WatchTarget $target)
  {
    $this->target = $target;
  }
  public function getTarget()
  {
    return $this->target;
  }
}

class Google_Service_Forms_WatchTarget extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $topicType = 'Google_Service_Forms_CloudPubsubTopic';
  protected $topicDataType = '';


  public function setTopic(Google_Service_Forms_CloudPubsubTopic $topic)
  {
    $this->topic = $topic;
  }
  public function getTopic()
  {
    return $this->topic;
  }
}

class Google_Service_Forms_WriteControl extends Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $requiredRevisionId;
  public $targetRevisionId;


  public function setRequiredRevisionId($requiredRevisionId)
  {
    $this->requiredRevisionId = $requiredRevisionId;
  }
  public function getRequiredRevisionId()
  {
    return $this->requiredRevisionId;
  }
  public function setTargetRevisionId($targetRevisionId)
  {
    $this->targetRevisionId = $targetRevisionId;
  }
  public function getTargetRevisionId()
  {
    return $this->targetRevisionId;
  }
}
