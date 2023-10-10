<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%teacher_work_plan}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_id
 * @property int $edu_year_id
 * @property int|null $semestr_type 1 kuz 2 bahor
 * @property int|null $course_id
 * @property int|null $semestr_id
 * @property int|null $student_count Talaba soni
 * @property int|null $student_count_plan Talaba soni reja
 * @property int|null $lecture ma'ruza mashg'uloti
 * @property int|null $lecture_plan ma'ruza mashg'uloti reja
 * @property int|null $seminar Seminar mashg'uloti
 * @property int|null $seminar_plan Seminar mashg'uloti reja
 * @property int|null $practical Amaliy mashg'ulot
 * @property int|null $practical_plan Amaliy mashg'ulot reja
 * @property int|null $labarothoria Labarotoriya mashg'uloti
 * @property int|null $labarothoria_plan Labarotoriya mashg'uloti reja
 * @property int|null $advice Maslahatlar o'tkazish
 * @property int|null $advice_plan Maslahatlar o'tkazish reja
 * @property int|null $prepare Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish
 * @property int|null $prepare_plan Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish reja
 * @property int|null $checking Oraliq va yakuniy nazoratlarni tekshirish
 * @property int|null $checking_plan Oraliq va yakuniy nazoratlarni tekshirish reja
 * @property int|null $checking_appeal Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish
 * @property int|null $checking_appeal_plan Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish reja
 * @property int|null $lead_practice Bakalavriat talabalari amaliyotiga rahbarlik qilish va b.
 * @property int|null $lead_practice_plan Bakalavriat talabalari amaliyotiga rahbarlik qilish va b. reja
 * @property int|null $lead_graduation_work Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish
 * @property int|null $lead_graduation_work_plan Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish reja
 * @property int|null $dissertation_advicer Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish
 * @property int|null $dissertation_advicer_plan Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish reja
 * @property int|null $doctoral_consultation TDYU doktorantiga ilmiy maslahatchilik qilish
 * @property int|null $doctoral_consultation_plan TDYU doktorantiga ilmiy maslahatchilik qilish reja
 * @property int|null $supervisor_exam Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish
 * @property int|null $supervisor_exam_plan Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish reja
 * @property int|null $kazus_input Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish
 * @property int|null $kazus_input_plan Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish reja
 * @property int|null $legal_clinic Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish
 * @property int|null $legal_clinic_plan Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish reja
 * @property int|null $final_attestation Yakuniy davlat attestasiyasini o'tkazish
 * @property int|null $final_attestation_plan Yakuniy davlat attestasiyasini o'tkazish reja
 * @property string|null $description
 * @property int|null $status
 * @property int|null $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Course $course
 * @property EduYear $eduYear
 * @property Semestr $semestr
 * @property Subject $subject
 * @property User $user
 */
class TeacherWorkPlan extends \yii\db\ActiveRecord
{

    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%teacher_work_plan}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'edu_year_id', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'subject_id', 'edu_year_id', 'semestr_type', 'course_id', 'semestr_id', 'student_count', 'student_count_plan', 'lecture', 'lecture_plan', 'seminar', 'seminar_plan', 'practical', 'practical_plan', 'labarothoria', 'labarothoria_plan', 'advice', 'advice_plan', 'prepare', 'prepare_plan', 'checking', 'checking_plan', 'checking_appeal', 'checking_appeal_plan', 'lead_practice', 'lead_practice_plan', 'lead_graduation_work', 'lead_graduation_work_plan', 'dissertation_advicer', 'dissertation_advicer_plan', 'doctoral_consultation', 'doctoral_consultation_plan', 'supervisor_exam', 'supervisor_exam_plan', 'kazus_input', 'kazus_input_plan', 'legal_clinic', 'legal_clinic_plan', 'final_attestation', 'final_attestation_plan', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'subject_id' => Yii::t('app', 'Subject ID'),
            'edu_year_id' => Yii::t('app', 'Edu Year ID'),
            'semestr_type' => Yii::t('app', '1 kuz 2 bahor'),
            'course_id' => Yii::t('app', 'Course ID'),
            'semestr_id' => Yii::t('app', 'Semestr ID'),
            'student_count' => Yii::t('app', 'Talaba soni'),
            'student_count_plan' => Yii::t('app', 'Talaba soni reja'),
            'lecture' => Yii::t('app', 'ma\'ruza mashg\'uloti'),
            'lecture_plan' => Yii::t('app', 'ma\'ruza mashg\'uloti reja'),
            'seminar' => Yii::t('app', 'Seminar mashg\'uloti'),
            'seminar_plan' => Yii::t('app', 'Seminar mashg\'uloti reja'),
            'practical' => Yii::t('app', 'Amaliy mashg\'ulot'),
            'practical_plan' => Yii::t('app', 'Amaliy mashg\'ulot reja'),
            'labarothoria' => Yii::t('app', 'Labarotoriya mashg\'uloti'),
            'labarothoria_plan' => Yii::t('app', 'Labarotoriya mashg\'uloti reja'),
            'advice' => Yii::t('app', 'Maslahatlar o\'tkazish'),
            'advice_plan' => Yii::t('app', 'Maslahatlar o\'tkazish reja'),
            'prepare' => Yii::t('app', 'Ma\'ruza va seminar (amaliy) mashg\'ulotlarga tayyorgarlik ko\'rish'),
            'prepare_plan' => Yii::t('app', 'Ma\'ruza va seminar (amaliy) mashg\'ulotlarga tayyorgarlik ko\'rish reja'),
            'checking' => Yii::t('app', 'Oraliq va yakuniy nazoratlarni tekshirish'),
            'checking_plan' => Yii::t('app', 'Oraliq va yakuniy nazoratlarni tekshirish reja'),
            'checking_appeal' => Yii::t('app', 'Yakuniy nazorat turi bo\'yicha qo\'yilgan balldan norozi bo\'lgan talabaning apellyasiya shikoyati ko\'rib chiqish bo\'yicha apellyasiya komissiyasi a\'zosi sifatida ishtirok etish'),
            'checking_appeal_plan' => Yii::t('app', 'Yakuniy nazorat turi bo\'yicha qo\'yilgan balldan norozi bo\'lgan talabaning apellyasiya shikoyati ko\'rib chiqish bo\'yicha apellyasiya komissiyasi a\'zosi sifatida ishtirok etish reja'),
            'lead_practice' => Yii::t('app', 'Bakalavriat talabalari amaliyotiga rahbarlik qilish va b.'),
            'lead_practice_plan' => Yii::t('app', 'Bakalavriat talabalari amaliyotiga rahbarlik qilish va b. reja'),
            'lead_graduation_work' => Yii::t('app', 'Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish'),
            'lead_graduation_work_plan' => Yii::t('app', 'Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish reja'),
            'dissertation_advicer' => Yii::t('app', 'Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish'),
            'dissertation_advicer_plan' => Yii::t('app', 'Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish reja'),
            'doctoral_consultation' => Yii::t('app', 'TDYU doktorantiga ilmiy maslahatchilik qilish'),
            'doctoral_consultation_plan' => Yii::t('app', 'TDYU doktorantiga ilmiy maslahatchilik qilish reja'),
            'supervisor_exam' => Yii::t('app', 'Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish'),
            'supervisor_exam_plan' => Yii::t('app', 'Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish reja'),
            'kazus_input' => Yii::t('app', 'Talabalar bilimini aniqlash bo\'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish'),
            'kazus_input_plan' => Yii::t('app', 'Talabalar bilimini aniqlash bo\'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish reja'),
            'legal_clinic' => Yii::t('app', 'Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish'),
            'legal_clinic_plan' => Yii::t('app', 'Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish reja'),
            'final_attestation' => Yii::t('app', 'Yakuniy davlat attestasiyasini o\'tkazish'),
            'final_attestation_plan' => Yii::t('app', 'Yakuniy davlat attestasiyasini o\'tkazish reja'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'subject_id',
            'edu_year_id',
            'semestr_type',
            'course_id',
            'semestr_id',
            'student_count',
            'student_count_plan',
            'lecture',
            'lecture_plan',
            'seminar',
            'seminar_plan',
            'practical',
            'practical_plan',
            'labarothoria',
            'labarothoria_plan',
            'advice',
            'advice_plan',
            'prepare',
            'prepare_plan',
            'checking',
            'checking_plan',
            'checking_appeal',
            'checking_appeal_plan',
            'lead_practice',
            'lead_practice_plan',
            'lead_graduation_work',
            'lead_graduation_work_plan',
            'dissertation_advicer',
            'dissertation_advicer_plan',
            'doctoral_consultation',
            'doctoral_consultation_plan',
            'supervisor_exam',
            'supervisor_exam_plan',
            'kazus_input',
            'kazus_input_plan',
            'legal_clinic',
            'legal_clinic_plan',
            'final_attestation',
            'final_attestation_plan',
            'description',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'course',
            'eduYear',
            'semestr',
            'subject',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery|CourseQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery|SemestrQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * TeacherWorkPlan createItem <$model, $post>
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->edu_year_id = EduYear::findOne(['year' => date("Y")])->id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!isset($post['user_id'])) {
            $model->user_id = current_user_id();
        }

        $model->lecture = TimeTable::find()->where([
            'teacher_user_id' => $model->user_id,
            'subject_id' => $model->subject_id,
            'edu_year_id' => $model->edu_year_id,
            'is_deleted' => 0,
            'subject_category_id' => 1
        ])->count();

        $model->seminar =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 2
            ])->count();

        $model->practical =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 3
            ])->count();

        $model->labarothoria =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 5
            ])->count();

        $model->checking = ExamStudent::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])->count();

        $model->checking_appeal = ExamAppeal::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])->count();

        $model->kazus_input = Question::find()->where(['created_by' => $model->user_id])->count();


        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * TeacherWorkPlan updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        $model->lecture = TimeTable::find()->where([
            'teacher_user_id' => $model->user_id,
            'subject_id' => $model->subject_id,
            'edu_year_id' => $model->edu_year_id,
            'is_deleted' => 0,
            'subject_category_id' => 1
        ])->count();

        $model->seminar =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 2
            ])->count();

        $model->practical =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 3
            ])->count();

        $model->labarothoria =
            TimeTable::find()->where([
                'teacher_user_id' => $model->user_id,
                'subject_id' => $model->subject_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0,
                'subject_category_id' => 5
            ])->count();

        $model->checking = ExamStudent::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])->count();

        $model->checking_appeal = ExamAppeal::find()->where([
            'in', 'teacher_access_id',
            TeacherAccess::find()->select('id')->where(['user_id' => $model->user_id])
        ])->andWhere(['edu_year_id' => $model->edu_year_id])->count();

        $model->kazus_input = Question::find()->where(['created_by' => $model->user_id])->count();


        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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
}
