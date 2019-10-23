<?php

namespace App\Http\Requests\Line;

use Illuminate\Foundation\Http\FormRequest;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LogicException;

class LineMessageApiRequest extends FormRequest
{
    /**
     * @var LINEBot
     */
    private $lineBot;

    /**
     * LineMessageApiRequest constructor.
     * @param  LINEBot  $lineBot
     */
    public function __construct(LINEBot $lineBot)
    {
        parent::__construct();
        $this->lineBot = $lineBot;
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        if (env('APP_ENV') !== 'production') {
            return true;
        }

        try {
            return $this->lineBot->validateSignature(
                $this->getContent(),
                $this->header(HTTPHeader::LINE_SIGNATURE)
            );
        } catch (InvalidSignatureException $e) {
            return false;
        } catch (LogicException $e) {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
