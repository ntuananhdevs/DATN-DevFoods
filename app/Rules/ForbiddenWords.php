<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ForbiddenWords implements Rule
{
    protected $forbiddenWords = [
        // Danh sách mẫu, có thể mở rộng lên hàng nghìn từ
        'địt', 'cặc', 'lồn', 'fuck', 'shit', 'dm', 'đmm', 'dmm', 'đéo', 'bitch', 'asshole',
        'ngu', 'óc chó', 'đụ', 'đụ má', 'đụ mẹ', 'đụ m', 'đụ mày', 'đụ cha', 'đụ con mẹ',
        'pussy', 'dick', 'cock', 'piss', 'motherfucker', 'bastard', 'slut', 'whore', 'rape',
        'faggot', 'gay', 'les', 'lesbian', 'anal', 'suck', 'cunt', 'wanker', 'jerk', 'bollocks',
        'arse', 'arsehole', 'bugger', 'bollock', 'bloody', 'bollocks', 'bollocking', 'boner',
        'boob', 'boobs', 'bugger', 'bullshit', 'clit', 'cock', 'crap', 'cum', 'cunt', 'damn',
        'dickhead', 'dildo', 'dyke', 'fag', 'faggot', 'fanny', 'feck', 'fellate', 'fellatio',
        'felching', 'flange', 'frigger', 'goddamn', 'handjob', 'hardcore', 'homo', 'jizz',
        'knob', 'knobend', 'labia', 'muff', 'nigger', 'nigga', 'penis', 'piss', 'poop', 'prick',
        'pube', 'pussy', 'queer', 'scrotum', 'shag', 'shite', 'shit', 'slut', 'smegma', 'spunk',
        'tit', 'tosser', 'turd', 'twat', 'vagina', 'wank', 'whore', 'như lozz',
        // ... Bạn có thể bổ sung thêm hàng nghìn từ ở đây ...
    ];

    public function passes($attribute, $value)
    {
        foreach ($this->forbiddenWords as $word) {
            if (stripos($value, $word) !== false) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'Nội dung :attribute chứa từ ngữ không phù hợp.';
    }
} 