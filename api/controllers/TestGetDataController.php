<?php

namespace api\controllers;

use api\components\HemisMK;
use api\components\MipService;
use api\components\MipTokenGen;
use api\components\PersonDataHelper;

use base\ResponseStatus;
use common\models\model\LoginHistory;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TestGetData';

    public function actions()
    {
        return [];
    }

    public function actionHemis($pinfl)
    {
        $hemis = new HemisMK();

        $data = $hemis->getHemis($pinfl);
        // return $data;
        if ($data->success) {
            return $this->response(1, _e('Success'), $data->data);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $data->data, ResponseStatus::FORBIDDEN);
        }
    }

    public function actionIndex($passport = null, $jshir = null)
    {


        // Define the Base64 value you need to save as an image
        $base64string = "/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkS\nEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJ\nCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy\nMjIyMjIyMjIyMjIyMjL/wAARCAF+ASkDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEA\nAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIh\nMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6\nQ0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZ\nmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx\n8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA\nAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAV\nYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hp\nanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPE\nxcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwDu\nrCwsTplmTY2hJt48nyE/uj2qf7BY/wDPjaf9+E/wpNP/AOQXZf8AXvH/AOgirFaG\ntiH7BY/8+Np/34T/AAo+wWP/AD42n/fhP8KnooAg+wWP/Pjaf9+E/wAKP7Psf+fG\n0/78J/hU9GaBEH9n2P8Az42n/fhP8KP7Psf+fG0/78J/hU+aKYEP2Cx/58bT/vwn\n+FH9n2P/AD42n/fhP8KnzRSAg+wWP/Pjaf8AfhP8KP7Psf8AnwtP+/Cf4VPRQBB/\nZ9j/AM+Np/34T/Cj+z7H/nxtP+/Cf4VYopgV/sFj/wA+Fp/34T/Cj+z7H/nxtP8A\nvwn+FWKKAK/9n2P/AD42n/fhP8KP7Psf+fG0/wC/Cf4VYopAV/sFj/z42n/fhP8A\nCl+wWP8Az4Wn/fhP8KnooAr/ANn2H/Pjaf8AfhP8KP7Psf8AnxtP+/Cf4VYooAr/\nANn2P/Phaf8AfhP8KP7Psf8AnxtP+/Cf4VYooAr/ANn2P/Phaf8AfhP8KP7Psf8A\nnxtP+/Cf4VYooCxX+wWP/Pjaf9+E/wAKP7Psf+fG0/78J/hViigCv/Z9j/z42n/f\nhP8ACj+z7H/nwtP+/Cf4VYpaAK39n2P/AD42n/fhP8KP7Psf+fG0/wC/Cf4VZooA\nr/2fY/8APjaf9+E/wo/s+x/58LT/AL8J/hVmimIrf2dY/wDPhaf9+E/wo/s+x/58\nbT/vwn+FWqKAK39nWP8Az42n/fhP8Kv/ANm6f/z4Wf8A4Dp/hUWK0Me1AHP6f/yD\nLL/r3j/9BFWKrWH/ACC7L/r3j/8AQRVipuULS02imA6jNNpaAFopM0UALS02jPtQ\nA6ikBzRQIWlptLQAtGaSigBc0UlJ3oAdRTc/zpNwz60gHj1opAeaWmAUUUUAFFFF\nAC0UlLQAUtJS0AFLRRTAWiiloEFLiiloAStGqGK0Me9MDmrD/kF2f/XvH/6CKsVW\nsP8AkGWf/XvH/wCgirFZlDqM02lpgLRSUZoAWikooAWikpaAD3HWlB70lM3hWxnr\nQBLSZ4/HFZ17rFlZxbnnjLdNobJ/KuSfxpeNetHDDheuBhmUDuRTFc733ycfWqc2\nq2EEjRtcJ5i9VByRXFajrV7qkfkwXBwQD8nyr7579a5O/wBQNtDJGsh3oTtd+Tno\nQfXBH60riueoN4rsvMdEWR9ndR1/zmq7eLofM2+XIvzYwQB16Z9q8Oi8U39vKwD5\nVjll7E4xTZvFF1I/zNkAcEdj60riue7jxGA4HkkqWIYbhkEdRU1t4mtbhmUgow68\nZFfP1r4n1CzeUw3DbZfvowyM9jz3qwnjPVUd83BdG6qwzRdBc+h4dQjuWHlOQoG7\nLDGRV1DuGQcg8g18/wCnfES/t5YzOA6xkgbTg7T2969N0Txvb30RXeS4wAMDJ+nZ\nqLjTO2FFVLe9huI90MgYDqOmPrVknjqOaYx9FNyKdTAKWkpaACloooAWloopiFFL\nRS0AFLRS0wAVoVQrQxQBy1h/yDLP/r3j/wDQRViq9h/yDLP/AK94/wD0EVYrMoWj\nNJRQAtFJRQAtFJRQAtGRmmbhnBOKw9f1wafaMltiS6Y4VAM7R6mmGxb1TWYbADcQ\nSQSAa4fUvF9w6PGvmlG4LI2P1H8qw9SvbnPn30rxejMM5/I8VzV5rxMreVMwUjqO\n/wBaT0JuasutxPEZRJKxQ4IZ8lT6jFTvrEJtRPM6mRVGxVO0hgfvfXpXEC/dC7Bi\nS/DAjrVWWd5Tl2Jx05pXJubc/iK4SVXt2MZXg7TjOTnpUN5rMmowkXTHcOhTgA//\nAF/61jEnuaTtikIeeud2aZ9aAcU/HHSgBo45pRxye1KBikIoAMk8mrCXk8LRmKRk\naPlCD93ntVftS9PwoA6rw54zv9IvEzOzQlsup5r2vRtfh1eISQyRurDcrKcY49PW\nvmncQf8A61beha5Npd5FIDlFzlc8c96Y0z6bjJ253bgR1NSdq880/wAaRp5e2USQ\nTH5FY/Mv4+ldpaapbXce9H6dR3+v0qky7mhS0wMGUEHIPpT6YBS0lOoAWlpKUUxC\n0tFKKAFFLRS0wCtHFZ9aNMDkrD/kGWf/AF7x/wDoIqxVaw/5Bln/ANe8f/oIqxWR\nQtFJRQAtFFITxQAucU0kd+f5UZGR+lc5r/iNbWR7G0YNchcuAeQPYdzQDZb13WFs\nbcpEd8zDgAZ2+5FeaXWredvO+Vuck8g1W1fXIYS0nnb5WOdo6iuPvNcubnKs7Fc9\n+DTbsQ2W9Y1KC7kKKm7B4kB5P1rAfk5Bz9aVzuG4de9RnmoJE5oFLingZ7UAM5pw\nFSeXx3pyoeoHSgCLb3p4PGCKlMXtgHtTTGfSgdiMjrijg/yqUQkj09KlNqyjii4W\nKuOBTcVYMZA5pvlnGKBWIcZNOTrTihGaAvHSgCzHdSI42ucgcYNdRp3iie28sB2+\nVQCQeSR1FccMg9KsQ7iSzcUDPoDwvr8epwbFbM4GXU9B/n0rq433A+o614F4e1tr\nGSNImcYbcGA5z6e9e1abercQCSN1ZceuCD15qkyka4pwqJJEdflbI9qlHTirGLTh\nSUtAhacKSnUAFOFJThTAK0az60aAOPsP+QZZ/wDXvH/6CKsVXsP+QZZ/9e8f/oIq\nesyxaKSigBCTuwPxozleO/FI5wQ3p1qlf6jHYWRmc5YcKvqe1AiHWdTGnWjbXHnb\ncIOuM9/wryrU9QjtvMkLiVmB3ODkZJ4z3Bp/iTW/MYvK26Rh04OP8K4G4mLSl0IQ\nnuO9F7ENjbyfz7hnbqT1zmqh5p5bJ5ABPcU0A1JI0cGnqvNOWPd2NWI4ecfd+tA7\nEaRE1YhtS7YC/pVmKADqpb3HStCKFgMKCufaocrFxhcpjTWUbnGB71bh01cAgckd\nKuLb8jAIPqV61YjtJASUfbnuOgrNyZtGCMuXTW2ndgccVVFspKr8xfuAK6JrdmXa\nXZ8dTgAVDFbJJcEKMqo6g8Ckpuw3TV9DKi08v91evQGrTWqhdpUqwHPHFawQKcQf\nNgYLdhS/YhKQJN3qcnFQ5spU0YUmno2zHcbTgVEdMYSMp49K6V4fsy7VAPB57imP\nCpkICgk/eY9KFVYeyRzjaSwbBwAPU9aryWQTjgntzwK6GSN13KRx1zmqU0W7d8uN\nvbFUptkumjCe3wfvD8Kiw+7g1psgzt6HsfWoXTAOVrVSMXErwidJAynGOfvdK9A8\nM+K7iJfssjjLNnfuyT0468V5+wYdAv41PaXZgbOBV3JPpXTbqO4tkKurZUfd7cVo\njrXmvw816K7WS3k4fAYP7gc/hXpIIzx3rVMB4pRSCnCmAop1IKWmA4UtIKdQAVo1\nn1o0wONsP+QZZ/8AXvH/AOgirFV7D/kG2f8A17x/+gip6xLFzSZHrSGjg8igAYhQ\ndx4ry7xlq7LM0RbCLkKue3+cV3uuXc1rplw0afMFGDn35rxvXr17mZpNg2/3ig/n\n1oJZyt7I8srZJb0qoBwV/EVYkYEnhce1RDGcUiBojJIIHB96nWIDtmljTcetXIIC\n74GcVLZcYjIImLBQuTWrBYb2HmMMd8irFnYHcBj6nsK2IrNMD5T9fWsZVDeNMo2+\nnRbgAM89uK1odPjXnH9anhjwMADHrirfljHBJrGU7m8YFdbWIj5VXj2prQHokK8e\ntX44cDkdan8gZHGKjmNOVGFJbyykRqpHrxU8OlhVCImT/ETWv5WzooHuBUqrzjOM\n+lVzk8pnLp20DcRx0Cij7GU5C4JNapQ03y+wPfrUtjRiXFuVjOBlyMGo3tjGqbRy\nB09a2ngDHJXoOKjePdyB070XCxz3lEFgRlWBFRT2IkjDo3zeo7VsS2vUA8ZyD70z\nyQGJ24z19DVKRLicjdWTHIYbZPTsfoay2yjbWyG967uS3iddrpuX3rK1DSoHj+UY\nYdG9K1jUXUxnT6o5GQH6iqz/ACt04+taV1bvFkEflWY43ZHpXRFnNJWN7w9rh0m9\nSRTjtx/hXufhzxDb6uqhXXzWGcDGR6181wtsmH1717J4D1Nvty+emJCojUHGAvsB\n+p9MVcSUz1Vc45p4qJCCeM89M96lFaDHCnCkFLTAcKWkpwpgLWhWfWjQBxdh/wAg\nyz/694//AEEVYqvYf8g2z/694/8A0EVYrEsKaeORTqPrQBz3iudE0zDruQgkjPXB\nGB+teXa2X+z/ADgc4yBx+A+leieMmeCCJlXcmDx6HK4/lXn2sgtDLK7kvtAJ6Bfa\nn0JZxEzKrkjI9BTUyzdaJcFzjn37U+3XLD9BUMlF2CAnG0e1b9npvlxh+Sx7mqem\nRq0u4kbV6cd66GHOQCOnauapJrQ6qUUSwW+xcY9+asbMn/61SRjIzUiqCelczkzp\nUQjTPHQfrVuOIAcVGqe1W0X5cUrlD1QAcdKkEY3A0qLzxUoCnAxQgI9g7c0qxj0/\nWpWGemKZjAq0SKFXZgfpTWTI6kU8EdKTd82OtAxhXjHWoyAOvcVMRgcVCXDkjOcc\nUgKkiLnpj6VAwGcAcCrcg5qu3GfWgTKzoKo3IAXrWkxGOn1qhdfdI9ehprcTOa1C\nJQCQMBv0Nc5P8khBH1rrbuPzI2Q1yt3G6EhhxnGa66bOOoiukIkkHBwT+Nes/Dq1\nt5X2mMqwOSAc56f/AF68rjfYNq9T1NekfDi8S21JE+UqyFd3ckkcVstzFHtATjqT\nUg+uaiiYOueualAwR+tajHinCm04UwHClFJThTAUVo1nVo0AcVY/8g2z/wCveP8A\n9BFT1XsP+QbZ/wDXvH/6CKsViWFFFIeeDxQBieJ7U3WkShAC8YLYPpXmWoQSvp7p\nF+7A5O5sMfoO9ep6/J5ej3BU7WK7QQOa8mvn8yGXz53UMNgC8MR7mmiZHFXJBfG7\nJHp/U0+3DZAHDHjPpTJkG/CoQo7etWbNQPnP8IzioZMdzdsNsQC9gfzNbkPqeCeg\nrn7AlpySeB/Ot+Elea5Kh2UjRj+7n0qVDUEZ4zmrCgZxXOzoRYTJHFWYewNVogOg\n/nVtBgf4UkmFyQZLYHbrUoQ/hTYx71Oo45q7MGxnfrQF5yRTtuTwelL7UxDAFX7x\n60oTk4qQqDQOMn0p2FchYY65qBkUElR15NW3wR1FV5Au0jH50NDuVnkAOKryDcOO\nadK4QHnp0rPfUI0UkkbsdM0uVkuSHzZHzHIz3qlKQRjmmtqSEZIJU+nOKaZo5l3R\nOCO+D0quVrcXMmU7iLepP61zmox5DE4z0P19a6rsQTXO6nEVZmHQ1rTeplUWhhQw\nvIfkDEg4JArp/D05tbuHy2ZVUgtgdTXL7cSE+/PtUsU+Jgql1UH+9gn6muo4z6Q8\nL6kuoWikTK7Y+Zc8r/8AWroweMDrXgWiahc2+qWIiDMQu2N0464z/WvebVnaCMyD\nD4w31q4sZOKcKQUtWMcOtOFNFOFMBa0qzq0aBHEWH/INs/8Ar3j/APQRViq9h/yD\nbP8A694//QRVisTQKQ9OmaWigDjvHErx20CKz/OSSBXnmq6vEtmkKWyhyOZXA4/H\nFd144nHmW/yHgna2a8p1qVmuHV2G7rlSOPr70+hDKMnlTOF3NwPnbHLH2qzYLvkW\nILgswC1nxShehO4962NGiL3aoBlmGST3qGEdzorDSykbSAHaWJGepHrVsLhgAOBX\nSG3VbONVQLhcCsi5jERz2UZNc1RO52QskMU4P+FH2xFfYpBI681gXerszmOE8dMi\nobfzV+6jsev1pRpdWJ1Ox1cWpxKRvIBzitOCZHGVkBHtXAy22rSuXW3YgDjkDFVn\nu9YtOJI3HYEg1fs09ifaNHqMbhiMEVYyOma8oi17UYHB8xuOzdK6TT/ED3BXeNr9\nD7VMoWRUaqZ2gIweaQe9Z9tctLnBBx3q5HIGB9ayNLkvOMDrTdzKvzEUDB5B/Cq9\n1IQpK85GcelUkDYk19BFne+MDnNYGp+KraLekaSOw4J6YpmpB5HYgA7gB171ix6L\n50u5icdAtaJRW5lJyeiK134omlUqigIe3eqkZ1G7IdYGK9d5XFdXp/hm1gPmSR+Z\nIfXoK2zbxqoUL0HYUOrFbCVKT3ZwD2V7jdsO7/ZqoZb20feynI68cGvQZo0U52g1\nmXdrHIvKipVXuinR7GTb3S3UIcHDdx6VV1GLzbVyPvDkVKLcQXBxnafSpJVBjbHp\nQrJ3QtbWZxcvDZBwc06K4WMqVUeYpzvIz+lOvB5dw644BqKPBcbR19a6lscj3PVf\nhopmu3upcsqAKhYnr/8AW4/MV7FDwnA46814n4F1GGG5tIGXy9pKttYncPpj/wCt\n617TE26PK4Pv1rSIFjPuKeM98U1eRnmnirGKKcKQU4UwFrQxWfWlmgRw9h/yDLP/\nAK94/wD0EVPUFh/yDLP/AK94/wD0EVPWJoGfakJGOtLSHn+lAHB+ObsW91BCE3+Y\npcqDzx/k15TrEsUku5YpIsjlTXo3j94n1ICZmRlXCY7/AIj8a8uvAsbkiQP9DwKG\nZsqA/PuIwTXVeD4fP1ZCecDPNcn96TNdz4Fh33pcfwDiolsXT3PRAuVaM+ua5zxC\nWWEwxrlpB+ldGxxKD04596ytQjWSQs3bj6Vm3Y6LHLWGkBQrTBmb36Cty3tUjIJU\nHHrTSQh55pPtIX+ICuaUm2axgkjVXYFA2g/hTZLeJ0IKjn2zWJNr9tbKxdy2OpXt\nVR/G+ngYAkb6CmoyewOUFuaN1pMByfKU561Tj0+KJsqMfSnweJ9PuwFW4Csez8Vb\n3Kw/wofMgXLLYmshsO0McVrx5GMdqwY5DHIM9Ca3baQMMk1HM76lcltiymSM4wR0\nqlePx6YrQXgEn8KxdSmKggdTT5hKJUK+c+1egqxGiQAcgnvUcSiK3Dt35Jrn9X13\n7MZFhBkdBliOi/8A16qKctEJtRV2db9pjQZZlVfUmq0uq2a8GdfrXnX9p6reXFuk\nbbGuQfK568kdfqDUemDVNXmdIbxhKq7gHPBFa/V2Y/WF0R6Ab+Gb/VyK2PQ1DJLk\nda4O4n1TT5ys8algeoHX8q1tM1a5uztlhCKP4t3J/Cs5UXHUuNZS0NWUDOfemsoM\nZ704qzLmlTpgj60kxtHD6qCt4/bmq0G8nAOeenStPX4dl+/51X0ywnu5cQpu5xzX\nbHY4ZL3jrvBlqzapGHbaucEA/lXvlsh2L5iLuI54715Z4G0gQ3KSSbHkRsYGOOe9\nerQsxQZXH41rECdUC9OPpTxUeecVIPrVDHCnCminCmAtaVZtaVAjh7D/AJBtn/17\nx/8AoIqeq9if+JbZ8H/j3j/9BFT5HrWJoGKjmPlwu+4gKpYn6VLVe8kjjtpPNbCb\nSGwO1AHkviK/adbq7kADSHCbcnP1/CvP5CJHyOpNd34klgubQ2q3qpcLJjyvmywP\nT61yI0y4tGRpIHLF8YIxgUSM7NmSv3skHca9I8AQ5ieX04/CuJlsnjMkuDtQ43Hj\nk16B8Pl/0CTHXv7VEkXS3OklcKGJPXpWJc3RLsozx1rVvgfnQdcZ6ViNEdxJ7muW\npJo7IK5nXV6y5yK5y/1O8diI4cp67s5rqrixWT3qt/ZSufm6elZwnFO7KlBtWRz+\njaR/bF0Pt11hAf8AVKcZqjc2UFpr9xBKPLjTzNmTwCFJT9cV2S+H4nIK5Vh0IPNR\nXnhZ7p/MkPmPjG5m5xXVGvE5pYeRyVrapfNpyKC11NMwlXPOMrtz+Ga7m806Tw9O\nFR2lsDyQxyYvofSq1h4fbS7lbm1O2ZR8rOQ2M96uz22o3qslxeZVz6ZpynGSsOFK\ncXcnljxArjB54I71esJuOc1nIj29jHas+8xnAY9xVmyDZFccrHUjoNw8mub1VyHH\n1reXO3rWNqUW85x0qUOxT1K5WDSlcOAzYUZPU1TtE0kaPcWUzsXnUlpVQnDHvmrs\ncayiMSKGVTxmrRtVzxGv5V0U58plOnz7nn0djqMU1tJCP+PcssLkDC5JOcfiTWr4\nf06+sJZmijXzHUIJG6KK62OBg2Ai4HtVsIxXbtAx7U5V5ErDxTOQudEkeTzbiUyS\nk85qaLS24wBx0rp/se85NOW2CA8Vk6knuackVsZQtwIAuMEVWERE4x61tSxYziqc\nSfv8Y+bBI+tJbiZyHjG1EOoK6jhkDCmaDanyJwWI8wAZPQDOccd8gV0vjLTxcPYs\ng5eLH61nTQrZ6UghOZDHh267emMe9dqaSOXkcpHQeB510rx1FYgnydQtzIpLE/MC\nRj8wefcV7GowuT2Havn20vmh8c+HPmKsihXA6nc5/pX0DC/mRI2Qd3Jx0rWm7pGc\nlaTRIue/epBTO1PHStSRRTqaKdQAorSrNFaVMDh7H/kG2f8A17x/+gip6gsf+QbZ\n/wDXvH/6CKnrA0GkD0qlqpRNPnLDkoRnrir2eMmuS8aah9l0S4bzVRpI2CkjJx0w\nKAZ5dpl4l740tJGyyGRnGfRVJ/pV2bUrqTUfIRg8AOCjjr+PWue0KdV8TWTyNjdI\nUPtuBUfzrp7S1P224BX5kYsfpXNWk1I3w0U4tkF5pYEHnQg+QW+dGOcH/wCtW/4D\nVoFuIXHO7PIqtDLtleKUBYpF25Y8Z7f4fjWxpCJa640cQOxo1IpwnzIU4KMro1dQ\njJcr0ArDdDvwO1dReR53Ec+9YUkYDE471lWRpTK6Q57VMtqGPQVNGmasqo9K5TdF\nQW+2nFDjGfwq7jNN2HPtTTsPcz/s5J5p4hCKcdRVwBfxpkhwKvmYrGVLGM5IHHrU\n1sm1hRL97OOadA2GpDNJULLxWfdL8xBrRR/l9DVK7U/eAoQMoxxAHFaEcWRg8iq0\nOC3P5Vei+VcDkU7CuAhHpUoQd6k7dPrSBxv2kHJGc44piEEYBproKmwCOKY/TPpS\nEUpFG3kc1SA2XSP2zzV+YACqSYa6RTyCelVHcmWwzxjHs/s8nPRunXHFc9dxFrfK\njIGM49K6bxgob7Io4MUO4/iay7UCSIBuQwIP5VrVfvEUlpc5KUEfEDT1DEBZrcZz\n0GR/jX0tbf8AHshHGBjivl+6mSXxyro3yrcxrkD+7gH9Qa+n7Vg8CsPcGuylokjj\nk7ybLA+tOFNHXjPvTvxrYkcKdTRTqAFrSrNrSoA4aw/5Btn/ANe8f/oIqfioLH/k\nG2f/AF7x/wDoIqc1iaEUzHGxc5Pf0rzn4nXEcdjbwqG3NJzz2A/+uK9H/iY57V4x\n8S9SNzrAgRsxwqMD1JGSf8+lJiZ5/JKyziSM4ZW3KfcV6nZul4DqUf3bq1Un6lhn\n8jmvKm9O/c16D4HuPP0Ga3Y5MEhC/Q4Yf+zVhXV43NcNK0mu43VVEzCIZOTjFdXC\nBHrSSrwEby8egArm51xqK9Bhs5roVYNcFh/y1XzB7MOo/nWNOVjoqI6q75tNw5OO\nKwJz8/tW7A4uNP8AU4rDvFMZ/GrraoimNibnpVtGrNjDmYHPy+lX1561zWOgmLAH\nr1pjOAcck+lIIS0wY9McD0qXywOcc0WK0IwO/TP61FN8qbsVc2jH4VSv5ljiJPpT\naE2Zjyc46nuanh9fWqaRuRvYcnnFaMCfd4/GlYaLsMZYbqSZODkZq5BhEXB61Fdb\nUY07aXE9TFlHlMW6Vbs5A6+1QzMr8EZzUds4t5CvY9KCTXHIGOlKRhsHnikiIKZF\nSHO3JHNUhjFyP6Uhagk96Y5A5pMRWuG4IrOtWLX0aju3FW7p+DVbTB5l+g96qKuz\nOWwzxbdZ1NogMt5SLgenWsy3ka3s5JnUjZG0gH0UmrV88c2uXs0gztkKL9BxVTxF\nK1vp10wwsT2Y24HVj8p/XPFW/emTfliedWBJu4nY/Mzg5PrnvX1TpE4l0yCUHO5F\nP6f/AK6+WrT5SD1G70r6Q8FXH2nw9bKeWSJTnPXjH9K7YvU4eh1SjAp1RRElcHtx\nUtbDHCnUwU4UCHCtGs2tKmBwtiR/ZtnyP+PePv8A7IqVnHODziorD/kG2n/XCP8A\n9BFSFggLfj9awNCKUr5WxuhHzY7Cvn3xVeLfa/eTocxtKdvHYcD9BXtHirUW0/QZ\n/LYfaJl2Ic9DyT+QFeB374kI5yexqWJ7FBzlia6jwPdmK/ubfOTJGJAPUqeR+RNc\nt0/GrWl3rabqlveL0if5h6qeCPyNKavFomEuWSZ6kbG2vVmuI2J8oDjoeT3pLE/Z\nJFSWbcVlDrnsOhFWosWVvJOpGLghUz/EvXP8qSe2i2B85kYZx6VwbHo3udJpb7Va\nP0JFVdST5yfxql4dui5lhY/PG2D7ita/jyyn1HWul+9ExXuyMcfLJ+NW4znHtVWc\neXhs8VLA4I+tczVmbp6F8devWnjpUCtng44p27a2M0DHnOax9WPzf7NbKc1UvbZZ\n0KnvQO5mxyRhAxPamDUoRJsWVC4PQHmqFzaygGF1baeNwNUofDVoJPNXcHzncOop\nqK6hJvodXFf5QZNZ2p6vHbxNJNJtQdTVRYriMFNxYDoTTP7P+0PvuF3Y+6DQkhO5\nHp/iCzvGwjOGPQOpGa0kczXQA57mqBscfLDGiHPLAVs6fa+SvQlvU96HboJXtqad\nuNsXuKeX4GaRRhaRxxSGIWBBwKhkfg0M2M1WmlHTNK4ipdPwas+HYN10ZznCZ/xr\nNnbe2wd63tFjaCzmz1WJj+lbUY3ZlUehzF0hjvEnxujMv7wex71l+NneDQ7aAnh7\nlufUAZH6mpP7Zt59PdxICSOAD71g+OdXF7qVvYxf6qyjw3vIcbvy4H4U6MXztszr\nySjoY1o3zAe/Fe8fDm9P9gRM5G1HMZPoDyP1rwKA7lGOCDXtPwzulfTZrfOQX4B6\nA46fzrrXxHMtj1MfLJj8alBqpC5aJME5Xseo7VaUkjoK2Qh4p4pgz6U4D1NMBa0q\nzQPc1pc+tMDhLL/kG2nP/LvH/wCgipOCxYnCoOM1Wsy50y0+7tFvH+Pyiq2r3rWW\nmyTf8tGO2MY/iPT8uv4VgaHIeN79HiuXLHKD7PAvQbm++34AYrx+6ctMSSSfevRv\nGkbWOh2cb/NNK/zFuqLjIH1JOT7mvNpOZnNSiZkfGMnrTOoIP407BOMUu3A56mmZ\nnrHhzF/4d0k3BDiO2fv12uV/kBUk1zHErMxAriND1df7MOlyXZs5YnMtpcZwFJ+8\np9j+XJqKS9vYpjNfX9tIiKSiRsG3noOB+dccqTcmdlOslE6fwvrcdx4tkgjbMbIV\nz6mvRLj5gB6V4B4evjYeIbS57CUZ+hNe/M4eMMOhGQfat+VR0RMZ82plXkZCHjIN\nVImxjB6VqTrviPYgc1jsSsmBXNUR0QZoxvkdamDLkfrWeJCPfNTJJsX1yazWhoaA\ncHoMU2RuOKr+btGfWmibcaGwvqDxK457VG0IHRRj0FShs80hkVTyRx60IL6kRgyM\n4pRANp4/OkkuSxKp83uKSO5Vf9Z8uPU0x2YiQCNix53dqsocVVW8iLNuIxng1IJV\nP3WGKTvcTui7vyMVEZMEg1XWbD4zRMxxkUhJhK/JwaozyYU9MmpWfkHNU7luDQhM\nZb/NKX64PGa2oLvZpl9IM4jtpDk/7prCUlYhin6pcfZfBmsScZaERgn1YgV1UXrY\nwqbXPLLe9S0uPPWDcy8oGbgHsfeqkkjyytJIxZ3JZmPcnrQQSOKRf7prpscdyaBg\nDg1618LrpSL+1/5aqFnU452jhvyyK8iThq7XwJqTad4ht7jkoMrIAeqHhh+XP4Uu\no4n0FbScyRv94DcCO49R+hrQU8kEc9enWsiIbY1z/dIBHPH1+lasbZCBiN23PHet\n0BMKdTR0p1MB1aOPes2tKmBwdnxpNpgZzbxjA/3RXPSuNY11pT81nYkquD/rZe+P\nYYx+Bq7c3Mv9lWFnbybJ57dPnHWNAg3P9ew96W2tYdM01VjQIUXEaenp9fU1zs0R\n5p8S7stqdraZBaNWZ8Hq7EZ/LGPwrzyTnefeul8YXa3Piecq25Y32Bu7Y7n6nNcw\n33R7nJoM5CZzj34p5HAI/Go1OAc+uamIAFBJASM0h6UE88UZzx1oAZypyDgjkV71\n4d1Eal4btbndlvLCtn1HBrwZhXpPwx1UGG60xzyp8xOex61MtjSk7Ox3r4ZfXNZN\nwm18/lWvIMLxWfcDchA6iueaudcXqUd5wCOtTxybjgEAioAOcHtU6oMEjjiuc36D\nbi4ES/M361WGrW8I+ZwT6VUv7I3TgF3Cj0NZE2heVIWjmc+oJziqjFPcmzvobja7\nvO2IY9zUDXZk+85rMjsgRteVz6c4xVuHR1lB2u+P9o1okjaEWON+YpMk7VB5OaQ3\n5ctJuBXOCc0+TQAVKmQ7P96pLfQYIxgHdnnk0aF8jKkupRKCARn61CNVMROyTAHG\nM1pS6ZAq5KgDvVM2EMjhRGoHQ8U9CZx0C31trmdY03E+oHArpVk8yHPp1qpY2MNv\nHtjjAz14qyy7BgVjJp7GCViPOB057VTuDkVcY4Xk89azJJP3hHpSSBkwAIUc1keO\nr37P4dtrEffuZt5x/dX/AOua1Y3xz6V594r1Q6nrbFDmKBfKT8Op/OuqitbnLVlZ\nWMYe9LjkHrSDmnAH04roOUFyGz61taJMbfUIJ92AkgJHt3/SsjAZeM5q5ZM4lx6c\n8elJ7FR3PpLRrlZNPWJFbEKjys/xp2/Tj8K6GIAwgddvA+lef+E743GjWbnAcAwO\nR+HX8Oa7y0lQghSeDyD1FawY2i0jEfe/OpRUeMc04cdOlWIfWjWdn14rRpoDyvRY\npGs47q5x5zwRBtpyFXaNqj8P1JqTW7kWumzSAEMVKg/3fUir2nosel2rnH+ojPHQ\nfKK4z4gat5No1tG5BK7mwehzwP6/hXPsaHkt7KZL25cjDZP4Gs9/6VYOdjH1NV5O\nW6UGTGoMsSe3NTynjPtUKjgAdP51LL2AoAr4pQMnilwaMYNAhp9Kv6JqL6Rq9veK\nThG+YA9V71QHHJoI+X60AnY99hu0uIFkjbcrqGU1WkcbvrXH+C9XdtOS1lcnYSFJ\n/lXVSuG5BrmkdsXdEUnEm7tU6YYYqo0mVxSpKf4TXPLc2TLUsQZcADiqrqMnIqbz\nj1xSMA5yehpFGe9sOoGfpTopViBBYr9RVloWAJU1C0THqARTUmWptDZL6LG0yg80\nR30HAElRmwLnhf0p66dsAIAz9KrmK9oxzFZ+RubPrSoi55/SnC3dRgmp449qjioc\nmQ5Nk0XABpZTnmgce1MkYADnmkQytcSAKT3rId8P9at30uCfSsuSZY0aR2AVRkk1\ncUTJkOs6p9jsGWM4lk+Vef1rh/JZmyMk96uXd82oX7SHhOiD0FWLMIzuMjcMc12w\njyo4Jy5mZRjZOSCPenofWtdkSQuiKAAeT61lXNs8DbwDtNWQOCle2RVm1jH2lcdC\neeKpo5K+46VespQJ434+VgWB+tJlI9Y8CSMBLAV2hWJXDc4AB/kwP4V6XD80keSN\nxj4b19j/AJ7V5b4N/cazcQLl2PzZzwGAI2j1yM16hYss9qjJyo5HrWkNimaccgb5\nTkHHAPepF64qrCxf5W+8P1qyB6dCM4rQkePQ1o7R6VmnI5PPvWnkeopgeamZYdKt\nMMQEt4yR2J2jivG/GN411fMpkJkkkLOu7PTp+WTXo+sXwtdCtrdGBnkgjALHplAS\nT9Bn8zXjd7cG6vnk6Ko2j/PrXMy27Ipv1wvQcCoCOcDljU0jfwj6UxVJfpz3pmY0\nDLAAfjTn5an7dg56moWf5iF/OgBCKaWA4A59aazcdaFRmHyqSTQIUAscAdal8vjp\n0rfsPDk/2RLqQDJGdp44qO7tBBbuGABJJFA7DPD0+yaWLOCCGFd5Z3wnj2scSDr7\n+9eWwzNb3Cyp1HUV1lnfCWJJY2wRXPVi07nRSlpY6pm55p0WAc1lwX6zjB4cdRVt\nJgO4rnaOhM0M1IhHfmqiSbhzVlD0xiszQurGHFP+yKRimQZ2fMefarStxiqAg+yg\nGhoFUZ71OW71EWJPUEVQWIkiXPI5pdm0dO9SooAGTmo5XGMjjFSwRVl+UmqskoxU\nk0gwTWNeXYQHmpSuxNoivpgXIzXI65qXnN9lib5FPzn19qk1bWGJaKFvmP3mHauf\nznmu2lTtqzjq1L6IkjfY4Y9qkiuCsxYHAY81X7U3ODW5znRWV0rnC4MrthFxnJra\nPhnVLy28x/KRWbiJuuKxPC10sF67+WrybcJnse9dy2oXTR7pHZMddikAD6VnOoom\nkI8x5hd2s+nXclvOhVlOMGnRyDIYV2+u6c+qRrIixtcKMfNxvH+Nc1P4Z1G2w7Rh\nM9MsCKpSUloS00zp/BGr+Xr1ir4YMfK+Y4GPTNezW8rWd8QD+7kOGB7H1+mK+brf\nzrS4XeDHIpDA/TnIr2nwr4j/ALVCxzYM/lgnB4fHcehwf5dKuLtoV0PQWcC4jcfL\nuPf6VbjJx09qx42IQRMTsYZQnqD1/OtSBgyhuPm6Ed62RJYq95aelUa0aYHzfqt7M1g7sAEgtgpLHJyQufx5Ufga86kbZgcE\n5yTXRa5rcLWEVhbL8i4LtnOTjpXM7Gb5iMCuYbdxVBLjb196fxGvX6mpEBaF2RM7\nBkt2FUXctxnNAhXlLHA6Uzn7q9TSorSfKgLHPauw8N+FHk23VyuM8qCOlGwkmzF0\n7w/NdYklDKp6LjrXUWHhxFkjVkAGckYrq7fT44Ewi4Pc4qa2twZS34UrlqJG9mFt\nSu0cDgY6V554gfEr5GMGvULgbIyBgjHNeWeLf3V0wz1AIoiEjmc5JPvVi0u3tZQQ\nfl7iqq/dpelU1chOx1ENyJUEkTYNX4NSOQr8N/OuRtLpreTIPymthZ45lBzg1hKF\njeM7nWW92GxzWpFICOtcJFcSREDO4dq1bbWAuAxxWEqb6G8anc7GGYg81OLhR1b6\nVzsOqI461Ib5DzkZrOxrzI3vtIxgmoWuBjI7VinUVC8sBVd9QT/npTSDmRvveEJx\n+FQtcErljg1gNrEaDBcGs288SRxggNk+gqlBvYl1Yo3b6+SGM5bgd64nVdZaZ2SF\njjuapX2qz3rHJKp6A1Rrpp0uXVnJUrc2iDvRRRW5gFNYU+mnpQBf0UlbolThh0r0\nC0Tdbgjb5jfhn6n0rgdCG6/xgnjpXo1pErWwbaEVVwdygj8BXNWRvSISzEEEnOP/\nANZqJ5GePypJCUHbrVx0DAr97jhh2/rUEduyyZOD/h9Kxu0acpjT2jSfLcDMR5Rj\n2/8Ar1b0O4m0u7CDdLbnDxsMB0Yf3ffqMdwelaTxb4/KcgjOcelY0yMHaKc5UcY7\nfWto1GRKFj1/w/rkGo2uAyyOvyyxAYdD/eA6gYrpbd/LURiTIwCpNeIeHtUSG9gt\nbhmaVR+6lPDEY6A9q9V0m++0qiRlH3DgEkE4649x6V0wqJuxm1odSsmfT3xWpz/k\nVzySmKSON0xv4UjnPtWtvP8Afk/75Nb3JPkR7aKC1gyQ8jLud/TPb8Kz5pCxPOBn\ngVevLlZY4LeEb3CgALzzXTWvhnTdFt47rXWE2oGPzBYlvlHpk46+1cw/Q5K2t9Qu\nLF44ldbTdvkZvlQn3NWbXw887cyg5+7gYBrpjDJqrLcybkgU4jhIwAvbIHHBrS0y\nN2vY08plU/7PP+R/WmJIh0Pwh5DLNcRqABkL3/GuuSEIAAMACrqxjFBizyazbNEr\nFJhxU9rBhMnvR5WWAq3DH8oB60DKF8AIyOmOa8j8Zt/xMVUdx+lew6muIc47V4p4\nqlL60yE5CDgelXEzmY46UlAopkBT0ldDlTTKSgC9HqDKPmFS/wBppjlCazKKXKiu\nZmsupxjoXX6U/wDtcdpX/FaxqO1LkQ+eRrtqwJ/1jn8KhfUzj5Qx+prNpaORC52T\nyXk0n8WPpUGTnk80UVVkTcKWjFFMAooooAKQ9KWigDQ0Hb/aa7umD+NekWwhzufJ\nQqOAOc9vwrzjQcf2ohI6AmvSLYNLGuRgqegTr+Nc1fexvRFWTBYMgPI+bPU+g9qg\ncN9oZsgLn5gOv1B+tajKHYxjCtjJCjB/wNUJlaOQDZjsQRg/jXOtjfqMQq25SpBU\n5PYqP8TVHUYt0SyZJYnk+ntVrZ+/ZT94nJYH8iaR4Va3ZAdxIBX5ckY/z1pxdmJq\n6MK4jAi82MMJY2BVh1BrtvCetROsbqSGb/WqTyG/vdeK5QI2DkMefmA6VSFydL1L\nzkLLG/3gP1HFbK72Mtj3mDXVkiCSqJFHUg55/wAa2/7atf8AnpL+VePR3ImtobyL\ncEcDeA2OfX6mtD7fP/00/wC+qr6zJDVK+xgz/wBl+HbQXVslmdRkhj2xsD8nH3uO\nhrNsrWbVbsT3UzPGsh8lmbOM88k9QDmucty1/dF5X3KAGLgnniultNRbzBFsDIPl\nGw4I/wAa3SsY3ubcaiSJmHzkjLAduxH51saXo/2ZzcOzFmGAD2H+NO0a0Ur9oZlZ\nieFHatvGalspIqgYbmpOMUsiDtTQTjnrUlDUXL81cVPl461WjGX/ABq4oyKAMzUl\n/cNn0NeEeIiTrlwPQ4r33UFBgbI4ArwPxCD/AG/dg9nxVozkZmaKO9FUQFFFFABR\nRRQAUEUUUAFJS0cUAJilopKACl70lLQAUUUUAFO6CkpGNAGl4fBOqpg4wK9Mh2Ik\nUm1WYjksTxXnHhbH9tpu6BSeRXo1pvl3OynB64XORXNX3OijsOnYRP8AKyYUZyw5\nGe596qtOHCbiR6d2Hufc1pNawFJHBXJGWHOT+BrP/dg8gFSNykDmuZI2epVwrMX2\nkNuwM8ZPf8BVuPzUGd0eA235h/Ce9JsQFQzkEjqO4qZMF8M4XJ3HAz9PwoYGPe28\n0NxymMnIAHTPrVK8tDJbMvRz0z610stmlxESzbsdXHJ/EA1jSq0Unlup3L0B6mrg\nyJK5U8O6sdPnjikkxaA4ZBGSWPua9B/tzT/+fWb/AL915Ze2nlSvKeVYj5cck/Xs\nKT7Rcf8AP5F/38FdFk9TEZZzR20RzGYyQMkjA6e9dRo8KCJbrZudsmMg9Me350/T\ntNstfjtoZm2MsYOejDgZx2qxqHhjUdLhkn0+4aVFIITGZGHcelappk2aNRZ5kljk\njAX5j8xH3uM8VqWeuwvsSdlUkZ354NcPb+KYx+7uFxIi7AiqWAINbMV3DNMFjCGI\nYcspHCt2PuDTaGmdqHSZAyMGU9CKhYFa5qOeVJA8ZRMZAKn06ipxrMkO1JsMcdSe\nTUtDTOih9atj6Vh2usRbE3qQzAYA5Na1vdQzj91IrYODtOcUrDIr8AxMc9Rg14D4\nlDL4jvA2Mh+1e/ahgQvz27V8/wDiPnxFen/ppVoiZn4zTaAadkEUyBtFO20mKAEo\noooAKSlooASijNFABS0lLQAUUUuOaQBilxxRkdqQmmAE000v1pDQBp+HSBqyE5I2\nnp1r03TwJIFO0DOTz1x9a808OqTq8YAJJB4r1K1Y2yYbCAgbcgnH41y19zejsWCq\nEKGVlVvlI9T6VkyyKs4ducOVODwMdAP8a0lmVg+wMSBgkncoHfkcis6e3j8tgVA3\nfdbP9e9c6NxrNG+DvweMY5/Gpo1jb+LcrHOEHQetQvGuMK/7tgFxjoB6VIsLE/eJ\n43Yxgn0PHahjRbiZGX5l53Z45IrK1C2CkyBiyocHK8t+Na1rI8uFC5cDAwPm/T/C\ntAaLLqEaiZdiAcFuo/TNOMW9iZNdThp9k4KSqpBX7oHFH9nQf884v+/a/wCFdBf6\nDDp8p33B+RfMZiMLj/Gq/naR/wA9l/WuhQlYxuiPRYRb2EMrgMzgNwOQMYx+ua6C\n21O4jQeaPMAGCvfg88/TmsmKE29hC4fAaNcYXkEj1NaEbnyVLH5mbA+h45rHmdzb\nl0L1zp2iaoNs0Ue/cR02kkj178Vy1x4N1DSbuN9LkaeNz+8BYIAO2fXjvWgRKTgl\nWIRlJPqh4NaFnqNxbI/mNvUFTtJzgGtY13szOVJdDll1mWGaRbqIwurAHAOCRx39\nRVy3mW7iUJtlUS7Q4PJPUf4V10umWGv26m5tlLcgE9R+NYt94NltgZbC9KBFLMsn\nO7v17V0KSexk4tbkUqrtjA5yvr1J6frU6SG3jQIsillzwcAY6jArk/7SubCVRLiW\nOT7i7uQD6tiumsp/MtjOoOXUkhj0ZepH1p2A2hqfnDyZTHux8pVuteK+JY2j8RXo\nbvJkfQ16rNu8rZ8pcbRuI7HofqK848Z2zRa0ZWIPmDBx6jinYmWpztFLRQSAJFOJ\nFNxRigB3GKTFNpaADFGKOfWjmgBMUuKSigBeKCRSYoxQAuaKKKACkoxmlxQAUhp2\nKaRQBteFh/xOUOcAKc8167a6PcXIV2by1I+/5hJI+nSvPfh3pyXF3dXkuCkAA2+u\na6nU/F165gtrNVg85WYt3CjsD6/hWUqfO9TWErI6pNK0+wjZ55RwNzM7BcCs691v\nw4g2NcQsWTcNq9R61gLpF1fxi5u5Yy0zbnCkklR0XJ7fzpG0a0muJGKCSRj8xcen\nTHt7U1SihObZpxz6BcRl/PEYCCRlYlSFPTIq7E2gRsWe8hPlj5laXP04rCk0iAhJ\nGRfnPYnrUD6HZecWWM/K+SS3Unr9aXs4j55G/J4p0W3jVdOjFzMWKqqIR9ecVn3G\nt+IdU/d28C2kDNgkNhwPr71LYWUFnIqJDGoAypVfWrckXMfOC4J7MP1H9atJIm7Z\nzd/ob3Ly3M9xPPcMPug54HbnnFUPKT/nxm/74P8AhXYOjR5QkHaQuOxJ6HFT/wBn\nyf8APVfyNMR//9k=";

        // $base64string = '';
        $uploadpath   = STORAGE_PATH  . 'base64/';
        if (!file_exists(STORAGE_PATH  . 'base64/')) {
            mkdir(STORAGE_PATH  . 'base64/', 0777, true);
        }


        $parts        = explode(
            ";base64,",
            $base64string
        );
        $imageparts   = explode("image/", @$parts[0]);
        // $imagetype    = $imageparts[1];
        $imagebase64  = base64_decode($base64string);
        $miniurl = uniqid() . '.png';
        $file = $uploadpath . $miniurl;

        file_put_contents($file, $imagebase64);

        return 'storage/base64/'.$miniurl;
        //    return LoginHistory::createItemLogin();

        // return getIpAddressData();

        // return 1;
        // $data = MipTokenGen::getToken();
        $pinpp = "60111016600035";
        $doc_give_date = "2017-09-28";

        $data = MipService::getPhotoService($pinpp, $doc_give_date);

        return $this->response(1, _e('Success'), $data);


        $mk = new MipService();


        // $xml = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date)); // where $xml_string is the XML data you'd like to use (a well-formatted XML string). If retrieving from an external source, you can use file_get_contents to retrieve the data and populate this variable.
        // $json = json_encode($xml); // convert the XML string to JSON
        // $array = json_decode($json, TRUE);


        /*  $xmlObject = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date));

        //Encode the SimpleXMLElement object into a JSON string.
        $jsonString = json_encode($xmlObject);

        //Convert it back into an associative array for
        //the purposes of testing.
        $jsonArray = json_decode($jsonString, true);

        //var_dump out the $jsonArray, just so we can
        //see what the end result looks like
        return $jsonArray;


        return $array ; */

        $rrrr = $mk->getPhotoService($pinpp, $doc_give_date);

        return $rrrr;
        return simplexml_load_file($rrrr);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new PersonDataHelper();
        //  $data = $model->services($jshir, $passport);
        $data = $model->services("30505985280023", "AA7231228");
        if (empty($data)) {
            return 'error-no';
        } else {
            return $data;
        }
    }

    public function actionView()
    {
    }
}
