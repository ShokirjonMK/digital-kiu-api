<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Building;
use common\models\model\Room;
use yii\behaviors\TimestampBehavior;
use Yii;

class CircleSchedule extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public static $max_student_count = 30;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'circle_schedule';
    }

    public function rules()
    {
        return [
            // required per comments
            [['circle_id', 'building_id', 'start_time', 'end_time', 'week_id', 'teacher_user_id', 'edu_year_id'], 'required'],
            // integers
            [['circle_id', 'building_id', 'room_id', 'week_id', 'abs_count', 'max_student_count', 'student_count', 'teacher_user_id', 'edu_year_id', 'semestr_type', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            // date/time stored as strings or safe
            [['start_date', 'end_date'], 'safe'],
            [['start_time', 'end_time'], 'string', 'max' => 10],
            [['zip_file'], 'string', 'max' => 255],

            // existence
            [['circle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Circle::className(), 'targetAttribute' => ['circle_id' => 'id']],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['week_id'], 'exist', 'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],

            ['teacher_user_id', 'unique', 'targetAttribute' => ['teacher_user_id', 'start_time', 'week_id', 'edu_year_id'], 'message' => _e('This teacher already has a schedule for this time and week')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'circle_id' => _e('Circle ID'),
            'building_id' => _e('Building ID'),
            'room_id' => _e('Room ID'),
            'start_date' => _e('Start Date'),  // Y-m-d (migration comment)
            'end_date' => _e('End Date'),  // Y-m-d (migration comment)
            'start_time' => _e('Start Time'),  // e.g., 10:00
            'end_time' => _e('End Time'),  // e.g., 12:00
            'week_id' => _e('Week'),  // hafta id
            'abs_count' => _e('Absent Limit Count'),  // nb lar soni
            'max_student_count' => _e('Max Student Count'),  // maksimal talaba soni
            'student_count' => _e('Student Count'),  // talaba soni
            'teacher_user_id' => _e('Teacher'),
            'edu_year_id' => _e('Edu Year'),
            'semestr_type' => _e('Semestr Type'),  // 1 kuz 2 bahor
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'zip_file' => _e('Zip File'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'circle_id',
            'building_id',
            'room_id',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'week_id',
            'abs_count',
            'max_student_count',
            'student_count',
            'teacher_user_id',
            'edu_year_id',
            'semestr_type',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'zip_file',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'circle',
            'week',
            'building',
            'room',
            'teacher',
            'eduYear',
            'enrollments',
            'circleStudents',
            'circleAttendances',
            'attDates',
            'dates',
            'attendDates',
            'my',
            'now',
            'selecting',

            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ];

        return $extraFields;
    }


    public function getSelecting()
    {
        if (isRole('student')) {
            $course_id = self::student(2)->course_id;
            $course = Course::find()->where(['id' => $course_id])->one();

            $useFall = ((int)$this->semestr_type === 1);

            $fromStr = $useFall ? ($course->circle_kuz_from ?? '') : ($course->circle_bahor_from ?? '');
            $toStr   = $useFall ? ($course->circle_kuz_to ?? '')   : ($course->circle_bahor_to ?? '');

            if ($fromStr && $toStr) {
                // Compose with current year (or schedule edu_year_id if that is a year value)
                $year = (int)date('Y');
                $fromTs = strtotime($year . '-' . $fromStr);
                $toTs   = strtotime($year . '-' . $toStr);

                return [
                    'start' => $fromTs,
                    'end' => $toTs,
                    'now' => time(),
                ];
            }
        }
        return Course::find()->all();
    }


    public function getNow()
    {
        return date('Y-m-d');
    }


    public function getMy()
    {
        return $this->hasOne(CircleStudent::className(), ['circle_schedule_id' => 'id'])
            ->andWhere(['student_user_id' => current_user_id()])
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]]);
    }

    /**
     * CircleSchedule uchun haftalik dars sanalari va ularga mos attendance ma'lumotlari
     *
     * @return array [ 'Y-m-d' => [student_id1, student_id2, ...], ... ]
     */
    public function getAttendDates()
    {
        $dates = [];

        // start_date, end_date, week_id yo‘q bo‘lsa darhol bo‘sh qaytar
        if (empty($this->start_date) || empty($this->end_date) || empty($this->week_id)) {
            return $dates;
        }

        try {
            $start = new \DateTime($this->start_date);
            $end   = new \DateTime($this->end_date);
            $end->setTime(23, 59, 59); // oxirgi kun ham kirishi uchun

            $targetDow = (int)$this->week_id; // 1..7 (Mon..Sun)
            if ($targetDow < 1 || $targetDow > 7) {
                return $dates;
            }

            // Start dan boshlab kerakli haftalik kunni topish
            $current = clone $start;
            $startDow = (int)$current->format('N');
            $deltaDays = ($targetDow - $startDow + 7) % 7;
            if ($deltaDays > 0) {
                $current->modify("+{$deltaDays} day");
            }

            // Har hafta shu kunni qo‘shib borish
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $dates[$dateStr] = $this->getAttend($dateStr);
                $current->modify('+7 day');
            }
        } catch (\Exception $e) {
            return []; // xato bo‘lsa bo‘sh qaytar
        }

        return $dates;
    }

    /**
     * Berilgan sana uchun shu schedule'dagi talabalarning davomat ro‘yxati
     *
     * @param string $date  'Y-m-d' format
     * @return array        student_id lar ro‘yxati
     */
    public function getAttend($date)
    {
        $dateStr = (new \DateTime($date))->format('Y-m-d');
        $attendances = CircleAttendance::find()
            ->select(['id', 'student_id', 'reason', 'reason_text'])
            ->asArray()
            ->where([
                'circle_schedule_id' => $this->id,
                'date'               => $dateStr,
                'is_deleted'         => 0
            ])
            ->all(); // faqat student_id lar ro‘yxatini array ko‘rinishda qaytaradi

        return $attendances;
    }



    public function getAttendance($date)
    {
        return $this->circleAttendances->where(['date' => $date])->one();
    }

    public function getAttDates()
    {
        $dateFromString = $this->start_date;
        $dateToString = $this->end_date;

        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }

        if ($this->week_id != $dateFrom->format('N')) {
            $dateFrom->modify('next ' . $this->dayName()[$this->week_id]);
        }

        while ($dateFrom <= $dateTo) {
            $dates[$dateFrom->format('Y-m-d')] = $this->getAttend($dateFrom->format('Y-m-d'));
            $dateFrom->modify('+1 week');
        }

        return $dates;
    }

    public function dayName()
    {
        return [
            1 => _e('monday'),
            2 => _e('tuesday'),
            3 => _e('wednesday'),
            4 => _e('thursday'),
            5 => _e('friday'),
            6 => _e('saturday'),
            7 => _e('sunday'),
        ];
    }

    public function getCircle()
    {
        return $this->hasOne(Circle::className(), ['id' => 'circle_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEnrollments()
    {
        return $this
            ->hasMany(CircleStudent::className(), ['circle_schedule_id' => 'id'])
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]]);
    }

    /**
     * Computed list of dates (Y-m-d) for this schedule's weekday
     * between start_date and end_date (inclusive).
     * Does NOT rely on localized weekday strings.
     *
     * @return string[]
     */
    public function getDates()
    {
        $dates = [];

        if (empty($this->start_date) || empty($this->end_date) || empty($this->week_id)) {
            return $dates;
        }

        try {
            $start = new \DateTime((string)$this->start_date);
            $end = new \DateTime((string)$this->end_date);
            // Make end inclusive
            $end->setTime(23, 59, 59);

            $targetDow = (int)$this->week_id; // 1..7 (Mon..Sun)
            if ($targetDow < 1 || $targetDow > 7) {
                return $dates;
            }

            $current = clone $start;
            $startDow = (int)$current->format('N');
            $deltaDays = ($targetDow - $startDow + 7) % 7;
            if ($deltaDays > 0) {
                $current->modify("+{$deltaDays} day");
            }

            while ($current <= $end) {
                $dates[] = $current->format('Y-m-d');
                $current->modify('+7 day');
            }
        } catch (\Exception $e) {
            // return empty on parse errors
            return [];
        }

        return $dates;
    }

    public function getCircleStudents()
    {
        return $this
            ->hasMany(CircleStudent::className(), ['circle_schedule_id' => 'id'])
            ->andWhere(['is_deleted' => 0]);
    }

    public function updateStudentCount()
    {
        $this->student_count = $this->getCircleStudents()->count();
        $this->save(false, ['student_count']);

        // var_dump($this->student_count);
        // $this->refresh();
        // var_dump($this->student_count);
        // die();
    }

    public function getCircleAttendances()
    {
        return $this
            ->hasMany(CircleAttendance::className(), ['circle_schedule_id' => 'id'])
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->semestr_type = $model->eduYear->type ?? 1;

        if ($model->start_date) {
            $model->start_date = date('Y-m-d', strtotime($model->start_date));
        }
        if ($model->end_date) {
            $model->end_date = date('Y-m-d', strtotime($model->end_date));


            if ($model->start_date >= $model->end_date) {
                $errors[] = _e('Start date must be less than end date');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $model->start_time = date('H:i', strtotime($model->start_time));
        $model->end_time = date('H:i', strtotime($model->end_time));

        if ($model->start_time >= $model->end_time) {
            $errors[] = _e('Start time must be less than end time');
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        // o'qituvchi bo'sh ekanini tekshirish (edu_year_id, start_time, week_id)
        $existingSchedule = self::find()
            ->where([
                'teacher_user_id' => $model->teacher_user_id,
                'week_id' => $model->week_id,
                'edu_year_id' => $model->edu_year_id,

                'is_deleted' => 0
            ])
            ->andWhere(['>', 'start_time', $model->start_time])
            ->andWhere(['<', 'end_time', $model->end_time])
            ->exists();

        if ($existingSchedule) {
            $errors[] = _e('Teacher is already busy at this time');
            $transaction->rollBack();
            return simplify_errors($errors);
        }



        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        if (empty($errors)) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if ($model->start_date) {
            $model->start_date = date('Y-m-d', strtotime($model->start_date));
        }
        if ($model->end_date) {
            $model->end_date = date('Y-m-d', strtotime($model->end_date));


            if ($model->start_date >= $model->end_date) {
                $errors[] = _e('Start date must be less than end date');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $model->start_time = date('H:i', strtotime($model->start_time));
        $model->end_time = date('H:i', strtotime($model->end_time));

        if ($model->start_time >= $model->end_time) {
            $errors[] = _e('Start time must be less than end time');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (empty($errors)) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        return simplify_errors($errors);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }

        
        return parent::beforeSave($insert);
    }

    public static function zipCertificates($model)
    {
        try {
            // 📂 Manba papka (certificates)
            $path = '/uploads/certificates/' . $model->circle_id . '/' . $model->id . '/';
            $sourceDir = STORAGE_PATH . $path;

            if (!is_dir($sourceDir)) {
                throw new \Exception("Source directory not found: " . $sourceDir);
            }

            if (!is_dir($sourceDir)) {
                return [
                    'status'  => 0,
                    'message' => 'Source directory not found',
                    'error'   => 'Source directory not found: ' . $sourceDir
                ];
            }
            // 📂 Chiqish papkasi (zip)
            $zipPathDir = '/uploads/certificates/zip/' . $model->circle_id . '/';
            $targetDir  = STORAGE_PATH . $zipPathDir;
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // 📄 Zip fayl nomi
            $fileName = 'certificates_' . $model->id . '_' . current_user_id() . '_' . time() . '.zip';
            $zipPath  = $targetDir . $fileName;
            $fileUrl  = 'storage' . $zipPathDir . $fileName;

            // ⚡️ Zip yaratish
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Cannot create zip file: " . $zipPath);
            }

            // 📂 Fayllarni qo‘shish (faqat har bir circle_student_id uchun eng oxirgi yaratilgani)
            // Fayl nomi formati: certificate_{circle_student_id}_{user_id}_{timestamp}.pdf
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $latestByStudent = []; // [circle_student_id => ['path' => ..., 'timestamp' => ..., 'basename' => ...]]

            foreach ($files as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $basename = $file->getBasename();
                if (stripos($basename, 'certificate_') !== 0) {
                    continue; // only certificate files
                }
                if (substr($basename, -4) !== '.pdf') {
                    continue; // only pdfs
                }

                // Parse: certificate_{circle_student_id}_{user_id}_{timestamp}.pdf
                $nameWithoutExt = substr($basename, 0, -4);
                $parts = explode('_', $nameWithoutExt);
                if (count($parts) < 4) {
                    continue; // unexpected format
                }
                // parts[0] = 'certificate'
                $circleStudentId = $parts[1];
                $timestampStr = $parts[count($parts) - 1];
                $timestamp = ctype_digit($timestampStr) ? (int)$timestampStr : 0;

                if (!isset($latestByStudent[$circleStudentId]) || $timestamp > $latestByStudent[$circleStudentId]['timestamp']) {
                    $latestByStudent[$circleStudentId] = [
                        'path' => $file->getRealPath(),
                        'timestamp' => $timestamp,
                        'basename' => $basename,
                    ];
                }
            }

            // Add only the latest files into zip under subfolder {circle_schedule_id}/
            foreach ($latestByStudent as $item) {
                $filePath = $item['path'];
                $relativePath = $model->id . '/' . $item['basename'];
                $zip->addFile($filePath, $relativePath);
            }

            $zip->close();

            // Modelga yozib qo‘yish
            $model->zip_file = $fileUrl;
            $model->save(false);

            return [
                'status'   => 1,
                'message'  => 'Certificates zipped successfully',
                'zip_file' => $fileUrl
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 0,
                'message' => 'Error while creating zip',
                'error'   => $e->getMessage()
            ];
        }
    }
}
