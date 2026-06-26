<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Form_Fields {
    public static function get_blocks(): array {
        return apply_filters( 'ucu_collegium_booking_form_fields', self::default_blocks() );
    }

    public static function submission_fields(): array {
        return self::flatten_blocks( self::get_blocks() );
    }

    public static function get_fields(): array {
        return array_merge( self::submission_fields(), self::legacy_fields() );
    }

    public static function fields_by_key(): array {
        $map = array();
        foreach ( self::get_fields() as $field ) {
            $map[ $field['key'] ] = $field;
        }
        return $map;
    }

    public static function active_fields( array $data ): array {
        return array_values(
            array_filter(
                self::submission_fields(),
                static function ( $field ) use ( $data ) {
                    return self::is_field_active( $field, $data );
                }
            )
        );
    }

    public static function display_fields( array $data ): array {
        return array_values(
            array_filter(
                self::get_fields(),
                static function ( $field ) use ( $data ) {
                    if ( 'attachment' === $field['type'] ) {
                        return false;
                    }

                    if ( ! self::is_field_active( $field, $data ) ) {
                        return ! self::is_empty_value( $data[ $field['key'] ] ?? '' );
                    }

                    if ( ! empty( $field['display_if_empty'] ) ) {
                        return true;
                    }

                    return ! self::is_empty_value( $data[ $field['key'] ] ?? '' );
                }
            )
        );
    }

    public static function is_field_active( array $field, array $data ): bool {
        if ( empty( $field['condition'] ) || ! is_array( $field['condition'] ) ) {
            return true;
        }

        $condition = $field['condition'];
        $actual    = $data[ $condition['field'] ] ?? null;
        $expected  = $condition['value'] ?? null;
        $operator  = $condition['operator'] ?? '=';

        if ( is_array( $actual ) ) {
            $actual = array_map( 'strval', $actual );

            switch ( $operator ) {
                case '!=':
                    return ! in_array( (string) $expected, $actual, true );
                case 'in':
                    return ! empty( array_intersect( $actual, array_map( 'strval', (array) $expected ) ) );
                case 'not_in':
                    return empty( array_intersect( $actual, array_map( 'strval', (array) $expected ) ) );
                case '=':
                default:
                    return in_array( (string) $expected, $actual, true );
            }
        }

        switch ( $operator ) {
            case '!=':
                return (string) $actual !== (string) $expected;
            case 'in':
                return in_array( $actual, (array) $expected, true );
            case 'not_in':
                return ! in_array( $actual, (array) $expected, true );
            case '=':
            default:
                return (string) $actual === (string) $expected;
        }
    }

    public static function format_value( array $field, $value ): string {
        if ( is_array( $value ) ) {
            $labels = array();
            foreach ( $value as $item ) {
                $labels[] = $field['options'][ $item ] ?? $item;
            }
            return implode( ', ', array_map( 'strval', $labels ) );
        }

        if ( isset( $field['options'][ $value ] ) ) {
            return (string) $field['options'][ $value ];
        }

        return (string) $value;
    }

    private static function flatten_blocks( array $blocks ): array {
        $fields = array();
        foreach ( $blocks as $block ) {
            if ( empty( $block['fields'] ) || ! is_array( $block['fields'] ) ) {
                if ( isset( $block['label'], $block['type'] ) ) {
                    $fields[] = self::normalize_field( $block, '' );
                }
                continue;
            }

            $block_key = $block['key'] ?? '';
            foreach ( $block['fields'] as $field ) {
                if ( isset( $field['label'], $field['type'] ) ) {
                    $fields[] = self::normalize_field( $field, $block_key );
                }
            }
        }
        return $fields;
    }

    private static function normalize_field( array $field, string $block_key ): array {
        return wp_parse_args(
            $field,
            array(
                'key'           => '',
                'label'         => '',
                'type'          => 'text',
                'required'      => false,
                'score_enabled' => false,
                'default_score' => 0,
                'score_map'     => array(),
                'options'       => array(),
                'condition'     => null,
                'block'         => $block_key,
                'display_if_empty' => true,
            )
        );
    }

    private static function field( string $key, string $label, string $type = 'text', bool $required = true, array $options = array(), ?array $condition = null, array $extra = array() ): array {
        return array_merge(
            array(
                'key'           => $key,
                'label'         => $label,
                'type'          => $type,
                'required'      => $required,
                'score_enabled' => false,
                'default_score' => 0,
                'score_map'     => array(),
                'options'       => $options,
                'condition'     => $condition,
            ),
            $extra
        );
    }

    private static function yes( string $field ): array {
        return array( 'field' => $field, 'operator' => '=', 'value' => 'yes' );
    }

    private static function no( string $field ): array {
        return array( 'field' => $field, 'operator' => '=', 'value' => 'no' );
    }

    private static function eq( string $field, string $value ): array {
        return array( 'field' => $field, 'operator' => '=', 'value' => $value );
    }

    private static function in( string $field, array $values ): array {
        return array( 'field' => $field, 'operator' => 'in', 'value' => $values );
    }

    private static function legacy_fields(): array {
        return array(
            self::field(
                'social_profile_url',
                'Посилання на Вашу актуальну та відкриту сторінку в соціальних мережах (Facebook, Instagram, TikTok)',
                'text',
                false,
                array(),
                null,
                array(
                    'block'            => 'personal_data',
                    'display_if_empty' => false,
                )
            ),
        );
    }

    private static function is_empty_value( $value ): bool {
        return is_array( $value ) ? empty( $value ) : '' === trim( (string) $value );
    }

    private static function default_blocks(): array {
        return array(
            array(
                'key' => 'general_info',
                'title' => 'Загальні відомості',
                'description' => '',
                'fields' => array(
                    self::field( 'email', 'E-mail', 'email' ),
                    self::field( 'degree', 'Оберіть ступінь станом на вересень 25/26 н.р.', 'select', true, array( 'bachelor' => 'Бакалавр', 'master' => 'Магістр' ) ),
                    self::field( 'bachelor_program', 'Бакалаври станом на вересень 25/26', 'select', true, array( 'theology' => 'Богословʼя', 'history' => 'Історія', 'philology' => 'Філологія', 'cultural_studies' => 'Культурологія', 'political_science_epe' => 'Політичні науки «Етика-Політика-Економіка»', 'sociology' => 'Соціологія', 'social_work' => 'Соціальна робота', 'psychology' => 'Психологія', 'law' => 'Право', 'it_analytics' => 'ІТ та аналітика рішень', 'computer_science' => 'Компʼютерні науки', 'robotics' => 'Робототехніка' ), self::eq( 'degree', 'bachelor' ) ),
                    self::field( 'bachelor_year', 'Курс станом на вересень 25/26', 'select', true, array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ), self::eq( 'degree', 'bachelor' ) ),
                    self::field( 'master_program', 'Магістри станом на вересень 25/26', 'select', true, array( 'theology' => 'Богословʼя', 'history' => 'Історія', 'cultural_heritage' => 'Культурна спадщина', 'ergotherapy_physiotherapy' => 'Ерготерапія та фізіотерапія', 'clinical_psychology_cbt' => 'Клінічна психологія (когнітивно-поведінкова терапія)', 'clinical_psychology_psychodynamic' => 'Клінічна психологія з основами психодинамічної терапії', 'journalism' => 'Журналістика', 'media_communications' => 'Медіакомунікації', 'human_rights' => 'Права людини', 'data_science' => 'Науки про дані' ), self::eq( 'degree', 'master' ) ),
                    self::field( 'master_year', 'Курс станом на вересень 25/26', 'select', true, array( '5' => '5', '6' => '6' ), self::eq( 'degree', 'master' ) ),
                ),
            ),
            array(
                'key' => 'personal_data',
                'title' => 'Персональні дані',
                'description' => '',
                'fields' => array(
                    self::field( 'last_name', 'Прізвище' ),
                    self::field( 'first_name', 'Імʼя' ),
                    self::field( 'middle_name', 'Побатькові' ),
                    self::field( 'gender', 'Стать', 'select', true, array( 'male' => 'чоловік', 'female' => 'жінка' ) ),
                    self::field( 'birth_date', 'Дата народження', 'date' ),
                    self::field( 'phone', 'Ваш контактний мобільний номер', 'phone' ),
                    self::field( 'photo', 'Додайте Ваше фото, зроблене не раніше, як за 6 місяців до заповнення анкети. (розмір файла до 5 МB)', 'attachment', true, array(), null, array( 'max_size_mb' => 5, 'allowed_types' => array( 'jpg', 'jpeg', 'png', 'webp' ) ) ),
                    self::field(
                        'social_accounts',
                        'Чи є у вас акаунт у нижчезазначених соцмережах? Якщо так, вкажіть лінк на акаунт.',
                        'checkbox',
                        true,
                        array(
                            'instagram' => 'Instagram',
                            'whatsapp'  => 'WhatsApp',
                            'telegram'  => 'Telegram',
                            'tiktok'    => 'TikTok',
                            'none'      => 'Немає в жодному з зазначених',
                        )
                    ),
                    self::field( 'instagram_url', 'Instagram', 'text', true, array(), self::in( 'social_accounts', array( 'instagram' ) ) ),
                    self::field( 'whatsapp_url', 'WhatsApp', 'text', true, array(), self::in( 'social_accounts', array( 'whatsapp' ) ) ),
                    self::field( 'telegram_url', 'Telegram', 'text', true, array(), self::in( 'social_accounts', array( 'telegram' ) ) ),
                    self::field( 'tiktok_url', 'TikTok', 'text', true, array(), self::in( 'social_accounts', array( 'tiktok' ) ) ),
                ),
            ),
            array(
                'key' => 'social_status',
                'title' => 'Соціальний статус',
                'description' => '',
                'fields' => array(
                    self::field( 'special_category', 'Чи Ви належите до однієї з цих категорій:', 'select', true, array( 'orphan' => 'Сирота', 'half_orphan' => 'Напівсирота', 'child_of_deceased_defender' => 'Дитина загиблого захисника / захисниці', 'combatant_or_veteran' => 'Учасник чи ветеран бойових дій', 'idp' => 'Внутрішньо переміщена особа', 'large_family_child' => 'Дитина з багатодітної сімʼї', 'none' => 'Не належу до жодної', 'other' => 'Інше' ) ),
                    self::field( 'special_category_other', 'Інше', 'text', true, array(), self::eq( 'special_category', 'other' ) ),
                ),
            ),
            array(
                'key' => 'health',
                'title' => 'Стан здоровʼя',
                'description' => '',
                'fields' => array(
                    self::field( 'health_status', 'Ви є особою, яка:', 'select', true, array( 'disability' => 'має інвалідність', 'physical_health_difficulties' => 'має труднощі з фізичним здоровʼям', 'mental_health_difficulties' => 'має труднощі з психічним здоровʼям', 'none' => 'жодне з переліченого' ) ),
                    self::field( 'disability_group', 'Група інвалідності', 'select', false, array( 'first' => 'Перша', 'second' => 'Друга', 'third' => 'Третя', 'childhood_disability' => 'Інвалідність з дитинства', 'no_group' => 'Немає групи' ), self::in( 'health_status', ['disability','physical_health_difficulties','mental_health_difficulties'] ) ),
                    self::field( 'disability_reason', 'Яка причина інвалідності чи реєстрації Вас, як особи з особливими освітніми потребами повʼязаними з фізичним чи психічним здоровʼям?', 'checkbox', false, array( 'vision_impairment' => 'Порушення зору', 'hearing_impairment' => 'Порушення слуху', 'musculoskeletal_impairment' => 'Порушення опорно-рухового апарата', 'physical_development_impairment' => 'Порушення фізичного розвитку', 'serious_medical_condition' => 'Важке медичне захворювання', 'mental_health_impairment' => 'Порушення у сфері психічного здоровʼя' ), self::in( 'health_status', ['disability','physical_health_difficulties','mental_health_difficulties'] ) ),
                    self::field( 'health_condition_details', 'Уточніть щодо наявного у Вас порушення стану здоровʼя', 'checkbox', false, array( 'blindness' => 'тотальна сліпота', 'partial_vision_loss' => 'часткова втрата зору', 'deafness' => 'глухота', 'hard_of_hearing' => 'туговухість', 'uses_crutches' => 'пересуваюся на милицях', 'uses_wheelchair' => 'пересуваюся на візку', 'depressive_or_anxiety_states' => 'депресивні та/або тривожні стани', 'personality_disorders' => 'особистісні розлади', 'eating_disorders' => 'харчові розлади', 'other' => 'інше' ), self::in( 'health_status', ['disability','physical_health_difficulties','mental_health_difficulties'] ) ),
                    self::field( 'needs_special_living_conditions', 'Чи потрібні Вам особливі умови для проживання в Колегіумі та/чи допомога у пересуванні по будинку та по кампусі?', 'select', false, array( 'yes' => 'Так (уточніть в "інше", яку саме потребуєте допомогу та умови для комфортного проживання)', 'no' => 'Ні' ), self::in( 'health_status', ['disability','physical_health_difficulties','mental_health_difficulties'] ) ),
                    self::field( 'special_living_conditions_details', 'Уточнення щодо умов проживання', 'text', true, array(), self::yes( 'needs_special_living_conditions' ) ),
                ),
            ),
            array(
                'key' => 'residence',
                'title' => 'Місце проживання',
                'description' => '',
                'fields' => array( self::field( 'region', 'Область' ), self::field( 'city', 'Населений пункт' ), self::field( 'actual_address', 'Вулиця, будинок, квартира' ) ),
            ),
            array(
                'key' => 'family_info',
                'title' => 'Інформація про сімʼю',
                'description' => '',
                'fields' => array( self::field( 'father_name', 'Батько' ), self::field( 'father_phone', 'Контактний телефон батька', 'phone' ), self::field( 'father_workplace', 'Місце праці батька', 'text', false ), self::field( 'mother_name', 'Мати' ), self::field( 'mother_phone', 'Контактний телефон матері', 'phone' ), self::field( 'mother_workplace', 'Місце праці матері', 'text', false ) ),
            ),
            array(
                'key' => 'previous_program_participation',
                'title' => 'Участь у формаційній програмі Колегіуму',
                'description' => '',
                'fields' => array(
                    self::field( 'previous_collegium_participant', 'Чи були Ви учасником формаційної програми з проживанням в Колегіумі раніше?', 'select', true, array( 'yes' => 'так', 'no' => 'ні' ) ),
                    self::field( 'previous_program_years', 'Вкажіть навчальній рік (роки) проходження формаційної програми та проживання в Колегіумі', 'text', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_curator_name', 'Прізвище та імʼя Вашого куратора (за останній рік формаційної програми)', 'text', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_listener_events', 'У яких заходах ФП Ви брали участь в ролі слухача/глядача? (Понеділкові зустрічі з куратором, Колегівки, молитовні заходи та ін.)', 'text', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_volunteer_events', 'В яких заходах ФП Ви були волонтером чи співорганізатором? (Понеділкові зустрічі з куратором, Колегівки, молитовні заходи та ін.)', 'text', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_program_experience', 'Опишіть Ваш позитивний та негативний досвід участі у формаційній програмі', 'textarea', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_cleaning_responsibility', 'Оцініть, наскільки сумлінно Ви виконували обов’язок прибирання у кімнаті/у світлиці', 'select', true, array( '1' => '1 (Не виконував/ла)', '2' => '2 (Дуже рідко)', '3' => '3 (Задовільно)', '4' => '4 (Добре)', '5' => '5 (Відмінно)' ), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_relationship_experience', 'Оберіть варіант, який найкраще відображає Ваш досвід побудови стосунків за час проживання у Колегіумі.', 'select', true, array( 'good_conflict_resolution' => 'Маю добрий досвід спілкування та конструктивного вирішення конфліктів.', 'asks_curator_for_help' => 'У вирішенні конфліктів завжди звертаюся по допомогу куратора.', 'avoids_conflict_talks' => 'Уникаю розмов стосовно конфліктних ситуацій та приймаю інших такими, якими вони є.', 'defends_position' => 'Відстоюю свою позицію. Не є прихильником миру "про людське око"', 'comfort_people_only' => 'Будую стосунки з людьми, з якими мені комфортно. Люблю, коли все по-моєму.' ), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'previous_community_contribution', 'Яким був Ваш особистий вклад у розбудову спільнотного життя Колегіуму?', 'textarea', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'student_organizations', 'До яких студентських організацій в УКУ Ви належите?', 'textarea', true, array(), self::yes( 'previous_collegium_participant' ) ),
                    self::field( 'curators_attitude', 'Команда формаційної програма включає кураторів (наставників), які проживають разом зі студентами на поверхах і дбають про формацію, дотримання правил та дружню атмосферу. Як Ви до цього ставитесь?', 'radio', true, array(
                        'value_care' => 'Ціную, що поруч будуть люди, які дбають про спільний добробут та мою формацію, готовий(-а) створювати добру атмосферу та дотримуватись правил',
                        'support_good_atmosphere' => 'Готовий(-а) до проживання на поверсі з куратором (наставником), проте не розумію, як я до цього ставлюсь, готовий(-а) жити згідно правил',
                        'ok_less_control' => 'Добре, але сподіваюся, що контролю не буде надто багато, готовий(-а) жити згідно правил',
                        'want_no_mentors' => 'Негативно ставлюсь до проживання з куратором (наставником) та дотримання правил' ), self::no( 'previous_collegium_participant' ), array(
                            'score_enabled' => true,
                            'score_map'     => array(
                                'value_care'              => 2,
                                'support_good_atmosphere' => 1,
                                'ok_less_control'         => 1,
                                'want_no_mentors'         => 0,
                            ),
                        ) ),
                    self::field( 'religious_staff_interaction', 'Формаційна програма реалізується працівниками, серед яких є духовні особи - священники та сестри-монахині, які проживають у Колегіумі. Якою буде Ваша взаємодія з ними?', 'select', true, array(
                        'familiar_environment' => 'Готовий(а)до взаємодії, оскільки маю досвід спілкування й співпраці',
                        'interested_to_meet' => 'Готовий(а)до взаємодії, але ніколи не спілкував(-ла)ся',
                        'no_experience_unsure' => 'Не маю досвіду спілкування, тому не знаю, як усе складеться',
                        'bad_experience_distance' => 'Маю поганий досвід. У спілкуванні буду тримати помірну дистанцію',
                        'dont_want_imposed' => 'Не хочу, аби мені щось навʼязували' ), self::no( 'previous_collegium_participant' ) ),
                    self::field( 'attitude_to_people_with_disabilities', 'Серед учасників Формаційної програми є особи з інвалідністю (потреби, яких впливають на спільний побут). Як Ви до цього ставитесь?', 'select', true, array(
                        'same_wing_help' => 'Повністю відкритий(-а) до спільного проживання, побуту та взаємної підтримки',
                        'same_room_positive' => 'Готовий(-а) проживати поруч і допомагати за потреби',
                        'positive_unsure_relationships' => 'Розумію важливість інклюзивного середовища та готовий(-а) вчитись будувати добрі стосунки',
                        'no_communication' => 'Не хочу спілкуватися з ними'), self::no( 'previous_collegium_participant' ), array(
                            'score_enabled' => true,
                            'score_map'     => array(
                                'same_wing_help'                => 2,
                                'same_room_positive'            => 1,
                                'positive_unsure_relationships' => 1,
                                'no_communication'              => 0,
                            ),
                        ) ),
                    self::field( 'emmaus_attitude', 'Формаційна програма передбачає спільні заходи з мешканцями дому "Емаус" (спільнота, в якій проживають особи з ментальною та/або фізичною інвалідністю). Якою буде Ваша взаємодія?', 'select', false, array(
                        'want_to_meet_volunteer' => 'Хочу познайомитись з ними та спробувати бути волонтером',
                        'volunteering_experience' => 'Позитивно, буду відвідувати їхні заходи',
                        'positive_no_participation' => 'Добре, але долучатись до спільних акцій не буду',
                        'not_interested' => 'Мене не цікавлять такі спільноти' ), self::no( 'previous_collegium_participant' ) ),
                    self::field( 'rules_attitude', 'Ваше ставлення до правил та обмежень?', 'radio', true, array(
                        'rules_with_exceptions' => 'Це дуже потрібно. Без чіткого виконання правил ніяк',
                        'breaking_is_interesting' => 'Правила потрібні, але завжди мають бути винятки',
                        'nobody_follows_rules' => 'Я не планую постійно жити в рамках'), self::no( 'previous_collegium_participant' ), array(
                            'score_enabled' => true,
                            'score_map'     => array(
                                'rules_with_exceptions'    => 2,
                                'breaking_is_interesting'  => 1,
                                'nobody_follows_rules'     => 0,
                            ),
                        ) ),
                    self::field( 'cleanliness_attitude', 'Ваше ставлення до порядку та чистоти?', 'select', true, array(
                        'always_cleaning_after_myself' => 'Завжди дбаю про чистоту своєї кімнати самостійно та готовий допомагати іншим',
                        'clean_after_self_only' => 'За собою приберу, за іншими – не буду',
                        'negative' => 'Негативно, оскільки порядок тисне на мене і я не можу нормально функціонувати' ), self::no( 'previous_collegium_participant' ), array(
                            'score_enabled' => true,
                            'score_map'     => array(
                                'always_cleaning_after_myself' => 2,
                                'clean_after_self_only'        => 1,
                                'negative'                     => 0,
                            ),
                        ) ),
                    self::field( 'new_relationship_readiness', 'Життя в Колегіумі передбачає спілкування та проживання з різними людьми. Оберіть варіант, який найкраще відображає Вашу готовність до побудови нових стосунків, вирішення конфліктів та вміння творити добру атмосферу навколо себе.', 'select', false, array(
                        'easy_conflict_resolution' => 'Легко знаходжу спільну мову з іншими та вмію конструктивно вирішувати конфліктні ситуації',
                        'friendly_avoid_conflicts' => 'Я не люблю конфліктних ситуацій, стараюся не провокувати на конфлікт та не зважаю на провокації',
                        'avoid_conflict_talks' => 'Уникаю розмов стосовно конфліктних ситуацій та приймаю інших такими, якими вони є',
                        'defend_position' => 'Буду відстоювати свою позицію. Не прихильник показового примирення.' ), self::no( 'previous_collegium_participant' ) ),
                ),
            ),
            array(
                'key' => 'motivation',
                'title' => 'Мотиваційна частина',
                'description' => '',
                'fields' => array(
                    self::field( 'motivation_letter', 'Чому Ви хочете брати участь у формаційній програмі "Християнська духовність у постмодерній добі" з проживанням в Колегіумі у 2025-2026 н. р.? Напишіть, будь ласка, короткий мотиваційний лист', 'textarea' ),
                    self::field( 'religious_experience', 'Який Ваш релігійний досвід?', 'textarea' ),
                    self::field( 'talents_hobbies', 'Які Ваші таланти, захоплення, хобі?', 'textarea' ),
                    self::field( 'mandatory_events_acceptance', 'Навчально-формаційна програма передбачає участь в обов’язкових заходах, у тому числі й релігійного характеру:', 'radio', true, array( 'yes' => 'так, я це розумію і погоджуюсь', 'no' => 'для мене це неприйнятно' ) ),
                    self::field( 'housing_rules_consent', 'Засвідчую, що поінформований(-а) про формаційну програму «Християнська духовність в постмодерній добі», хочу взяти в ній участь, розумію та погоджуюся з правилами проживання в Колегіумі!', 'radio', true, array( 'yes' => 'так', 'no' => 'ні' ), null, array( 'score_enabled' => false, 'default_score' => 0 ) ),
                    self::field( 'personal_data_consent', 'Гарантуємо, що зібрані персональні дані не будуть розголошені. Чи даєте дозвіл на обробку Ваших персональних даних?', 'radio', true, array( 'yes' => 'так', 'no' => 'ні (заява не буде прийнята)' ), null, array( 'score_enabled' => false, 'default_score' => 0 ) ),
                ),
            ),
        );
    }
}
