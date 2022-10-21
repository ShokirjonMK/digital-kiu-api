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
        $base64string = "/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkS\nEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJ\nCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy\nMjIyMjIyMjIyMjIyMjL/wAARCAF+ASkDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEA\nAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIh\nMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6\nQ0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZ\nmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx\n8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA\nAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAV\nYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hp\nanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPE\nxcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD0\njT7CxOmWRNjaEm3jyfIT+4ParH9n2H/PhZ/+A6f4Umn/APILsv8Ar2j/APQBVmmM\ng/s+w/58LT/wHT/Cj+z7D/nwtP8AwHT/AAqeigCD7BYf8+Fp/wCA6f4UfYLD/nwt\nP/AdP8KnooAh/s+w/wCfCz/8B0/wo/s+w/58LP8A8B0/wqbNFAEP9n2H/Phaf+A6\nf4Uf2fYf8+Fp/wCA6f4VNmigCH+z7D/nwtP/AAHT/Cj+z7D/AJ8LT/wHT/CpqXNA\nEH9n2H/Phaf+A6f4Uf2fYf8APhaf+A6f4VPmjNAEH9n2H/Phaf8AgOn+FH9n2H/P\nhaf+A6f4VPmjNAEH9n2H/Phaf+A6f4Uf2fYf8+Fp/wCA6f4VPmjNMCD+z7D/AJ8L\nT/wHT/Cj+z7D/nwtP/AdP8KmzS5oAg/s+w/58LT/AMB0/wAKP7PsP+fC0/8AAdP8\nKmzS5oAg/s+w/wCfC0/8B0/wo/s+w/58LT/wHT/Cps0ZoAh/s+w/58LP/wAB0/wp\nP7PsP+fCz/8AAdP8KnzRmgCH+z7D/nwtP/AdP8KP7PsP+fCz/wDAdP8ACps0UAQ/\n2fYf8+Fp/wCA6f4Uf2fYf8+Fn/4Dp/hU2aKAIf7PsP8Anws//AdP8KP7PsP+fCz/\nAPAdP8KmozQBB/Z9h/z4Wn/gOn+FH2Cw/wCfC0/8B0/wqfNFAEH2Cw/58LT/AMB0\n/wAKP7PsP+fC0/8AAdP8KnooAg/s+w/58LT/AMB0/wAKv/2fp/8A0D7P/wAB0/wq\nvWhSAxdP/wCQXZf9e8f/AKAKsVW08/8AErsv+veP/wBAFWKBi0tNzRmgB1FNzRmg\nB1FNzRmgQ6jNNzRmgB1FJmkzQA6im5ozQA6jNJmkzQA6im5ozQA7NFNzRmgB1FNz\nRmgB1FNzRmgBc0ZptFAD6KbRmgB1GabmjNADqKbmjNADqSkzRmgBc0UlFAC1o1m5\nrQoAxtP/AOQXZf8AXvH/AOgCrFV9P/5Bdl/17x/+gCrFIYUUUlAC0UlFAC0UlGaA\nFopKM0ALRSUUALRSUZoELRmkpKBjqKbRmgQ6im55oB4pgOo703NGe9ADqTNNz9aM\n+1ADs0ZpuaM0AOopM0ZoAWikzRQAuaWm5ooAdRTaWgBaKSigBa0M1nVoZoAx7D/k\nF2X/AF7x/wDoAqxVbT/+QXZf9e8f/oAqxSGLSUUUAFFJRQAtFJRQAtGabRmgB1FN\nzRmgB2aM0wtj61BPcxwIWlkCgehoAs7sdajMqjoc1y2p+L4LZCLaMOw/iY/0rh9S\n8bXM8u0zA4z8isePwAx+dTzodj1iXVLOIHfcICOwOTUA1ywZgBcdfavII/EDT8eY\noY9cgkfjUovpV4khTnuhxmp52HKeypdJKMo6keoNDXSouSeK8ag8UXNheIsUhIwP\nlbr9DWlfeKLl2O5woP5fgKfOHKelSatbRnDygfTnH5U6PVbRzgTrkevFeOt4iYNg\nSqc/7JU/pmrEXiCUHa6E4HVct/8AXpc7DlPZklV1yrcU/NeXWPieWPaYpiPQE/5/\npXU6f4sikdY7tfJZuN4+7TVRBys6jrRzUSSrIiupVkboynINPzVkjs0UlLQAUtJR\nQAtFJRQMXNFJRQIdmjNNpaAFrQzWdWjmgDHsP+QXZf8AXvH/AOgCrFV7D/kF2X/X\nvH/6AKnoGFFFJQAtJRRQAUUUUAFFJRQAtRs/pke4qK6uobWFpZnCRr1Jrgdf8Vvc\nJKkcht7YcE5+Zx7+n0FTKSiNK50GseKoLLdBZhZZhwT1C/8A164XV9furgkvKDu/\niY/KPw7muXvPEW/MFmv4k9ayXlcktJJI7Hso+X86y96W5WiNSe/hy0k1xLI+f4W2\ng+1Rf2lCV2pBCQeMnmsZ3mf7sK/XbUe2VuTub2C8CqSRNzca5WXKqRz/AAxMB/8A\nWpI7h4crmXaem4g/yrGCEY6g/WrCs20ZY59SOtDHqXftDxxh1cKztyz87cVRur0O\nxAZnP98960RbK1mm5go3Z554rLnjjQ8fOD70lZgQKwLfelHuD1q1BPIhASYMP9ok\nVV3BB/qyvvmjzUcYBFXYR0NvqNyhX96rD+7kMPy61uWWoxynDDbz0Ukgf1Fef7iD\nlTU0N1KjAhmUjoQcGocBpnrGlazd6Yxe3fzYm5eItkV3uka1batbCSJsMOGQnlTX\ngVh4iaN9lyDjtIgwwrqNP1aSF0vLOVS/QsvAb2YUlJw3G0me0A0tc/4f8SQaxEI3\nxHdr96Mn73uK3s+1bJ3V0QOpc02jNMB1FJmigBaWm0tAC0UlFAC1o1nVoUAZNh/y\nC7L/AK94/wD0AVPUFh/yC7L/AK94/wD0AVPQAlFFFIAoopKACiikNABmqd/qEVlb\nvLKwVVGSakuJhGh+YLgElj0UdzXlfirxJ9sLiNytpEfkB6sf7xqZy5UNK5F4l8VN\nduZJHKQLny4x3+tef6hqkl0+ZHKxA8Lnmob2+e5l3uTt6KoqssRP7xl2DsT3+maz\nUesin2QrzSE7YxsQ8jHenYZRmQn8Tz+VNNwygrCoCnqzck1GsTytn5n5qxWJfMBb\n5VH48mpkSWQ/xfnU9ppczkfu2A9hW9Z+HiQCQQfXNQ5pFqDZiwWrse/TvWjBZMB8\nyn8BXRRaN5agEg1cjsQpBIH5VhKoaxpnPzWwNmq/zrHu7B4x80ZPPYV3j2aMuNo5\n9qo3Omb1IAIzxmhVLA6Z5/JFtPIK+2KgYLnr+ldrJoTY5/Sse90d4znaCK1jVTM3\nTaMEZU8H8V5FL5gbrg+9SS2zJ0z+FVGBHfkVsmjKxMXxxgVPaahPZS7omAHcdjWe\nJMdTxT92eQabVxHc6fqInC3FvIySIcnDYKH/AAr1fwp4k/ti2+z3JUX0Qyf+mq/3\nh7+or54srySxuEmR8AHnnqK72x1AwvDe20xXOHRk7H/PBrLWD02L+I9uzS1maJq0\ner6etwAFkHyyp/db/A9RWlWydyBaKSimAtLSUUALS02loAWtGs6r/NAGXYf8guy/\n694//QBU9QWH/ILsv+veP/0AVPQAlFFFIBKKKKAEpkjYFPPAzWPruoiwsnIP7wjg\nenvSbsrgjlfGniFYoXsLdyGbHmsP0FeT6ndmQgliIV4Qd5D6/StPXdQLyyGRuC25\nz/T6mucuGO/zpj8/QD+7WK953NNlYjaUplj9/wBPSoCZJn6lmPrT4onuJBngVuaf\np3mNiNM+rEVTkooFG5SsdJeVgZDgH1rqLDRo028ZNX7DTEiAJGW7k1rxwKvAGK5Z\nVGzeMEiGCyRAAVzVtECjgYqVV46U/Z+FRc0SRCFGelO2n0xUgTJ6VJsyaBlfYRSl\nP9nNWChoCUCKrQqOcc1XltEccrnNaJjyKiZTTEc3faJDKuRGAfX0rlr7RWjzjBxX\no7pgdOKoXVqknUA1UZtESimeVT27RNhhg1AEyflOD6Gu61HRFcEoo57Vyd7YvbSE\nMCBXVCaZhKFilypwwPuK29CvjDJ9jlIMMpymexrIXkhW/AntT1QhvlbBB4/2TVPV\nWIWh634S1NtK1ZY5pG8mT9249B2P4H9Ca9TBPUYIrwfSrhr/AE9JBxcRfK/vjrXs\nXh3UP7Q0mF2P7xRsf6iopS+yypLqa+T6UuaSgVuQLS0lFAC0UUUALWj+NZ1aFAGV\nYf8AILsv+veP/wBAFWKgsP8AkF2X/XvH/wCgCpqACkpaSkAUlFFADXYKuT0715v4\n31MrvUNl89B244H4V317N5du7n7qgkn6V4z4mvJZ7jy42DTTHKj05rGq+hcF1OVk\nzLdM7/NBanLN/fk9PfFZUxe5lJ/hHLOTxn0FaWqXAgCabahf3f33A6seprPEbNJ5\neTgHBJ9aIrqN72L+l2puJQozs7mu9srFIYVCrjPasPQrVVReMD+ddZGuABXPUldm\n9OIqrg4A/GplHP8AWhUyalUY7VjY1HKMDgVIARSKDUuKtIBijnpTgvOe1O56UdT6\nVVhARkf1o2jFPCgd6Rs4zmgBhT261GV49qnyDjrTGH5UrAVXUdKrSp7VeZagYHJ4\nGKQjOliB6isnUdMjuYGBQZ9e9dBIntVZ0+U007CaPKtQs5LKcjHy5ogxcJhcbh29\na6nXrNZAwK9ehrjkeSxuvVR1HqK64vmRyyVmdHoV2bS6Q7vkkO0k9j6H+VeqeErw\nQai9vnEcwyBnjpwf6fhXjs0qC5S5QlYZxyQPusO9d54b1BysbOV8+3bdx3X2+v8A\nWs5aSUit1Y9gHJp1QwOJIlYHKsMj6dqlrpMh1FJS0wClpKKAFrRrOrRoAy7D/kF2\nX/XvH/6AKnqCw/5Bdl/17x/+gCp6QCUlLSUAJSHgUtNYkjigDn/E9yINMkUnAK7T\nz614zc3O2a81Bz/qxsj56E9h+Yrv/iBqXljyEycAkgd684ntzPILRiRHbr5kx/2z\nzj+n4VhLWRotEYiREKJnb5mO4+3/ANeprUB5txGFB6Co7mR7mb5RhR29K0NLszLc\nJEFyc5OO1VLRaiWrOs0aLKhyOO2OgroEXvVS0gEMSqBjAq9GtcT1Z1xVkPUVIFzx\nQuKeKdhjlXHBp4X8qQVIB7UwGgH8Kf1p22lA5qkgI2Az15pDyKm8vJBpPLODgUgK\n4HNKc88ZqXZge9NZSMimIgYcdahYcVOaiIFKwiu3NVpAM1cYc9arSLk0gMDWrcvb\ns6jkHtXBX8W7nPI5H+Feo3MHmwMCM5HSvP8AVIGtZ2Vwdh6kfz+tdFNmFRFDS38+\nGawkPySDdGf7jjoa6Twlfnz/ACJvlkjBUjHbuPwrmoYxBdRSggozc46YPcVp3GbD\nVbe8Hy5+9j1H+Iqqkb6ERZ774euPO0tFLZaFih/p+hrYrjvCF8rF4wRh1Vhjvj/6\n1dhV03eJMlZi0tJS1YhaKSlpgLWhms+tCgDMsP8AkF2X/XvH/wCgCp6gsP8AkF2X\n/XvH/wCgCp6AEpKWkpAIainkEMDSMQAoJqWsrWZP3JjOdoUu/wBKUnZXGjyjxFcN\nd+IovMJ2IGldf5D+Qrnbvzow1omDcznfKx/hyeSfp0rejtJ9S1K5ukX91G2Gdjxx\n/X2rG1O4jtpTFAp82TJdyOVA6Csaa0uXJoy4YY0n8iMLIxOCW/nXbeHtE2oJnGCf\nQcCuc8K6e15eDCkLnr3NevRWKW1oFK8kDPpSleTsVDRXMNoQhx6Ux5UhQu7YAqxe\nyJBG0kmQOw71zMz3V9M2EOAeAO351i1Y3TLz60mfkDEZ44qSLWIHI3yBfZiKz10i\n7nb+FARzvBJqCTw3eqciSJh6INtNJA7nTw6jbSYVZF/OtCOWMjAIPFcKNMuYXWQK\nUYHkdq0Ipp1AKMxZT8wz+oo0Fqdiu09DmnBec1i2l6xk2vwR1rajcOqmmh3HECmH\nA5p7cVWmlx8p4J70xD2dV/wqnLfQqcMwU1RnvGZCVzzxkVkXLszFShZj/COlLQDa\nfULYnCyIfo1VZNUhHG8Bvfoawhpl3MdyL5eeynFSDQNQcAtNGAOxAzTsLU101GNx\n82Meo5FTDDHIPXvWKdIuo+VkTcPfFSWtxLaP5cqkL254zU2Gbgg3oTiua8QaOXil\nZF5xgiuvtSrxhgCVI/Kp7nTxMW+XORgitVHS5k2eJIr2zltgeMH54z2reu7T7b4f\nNxEQ+zBGOox1/T+VV/Elo+lam0kagrkllPRh3Fa/h/y/JZ0/487gEFSfuN6fXH5j\nPpWlrq5lszY8CahiO0cnrhD7dq9aU5FeFaF5mm6hDbOMFboqCOh47fnXtenzi4so\nJOPmUcilDSTQS2uXKKKK1JFpaSloAWtCs+r9MDNsP+QXZf8AXvH/AOgCpzUNh/yC\n7L/r3j/9AFTUAJSUtJSAQ8da5DxXqBtdDu7jdtaWTy0+g4/xrrJDhGb0BxXkvxU1\nJrXRbWzQkM7bj+pqJ9ikcJbeLp7OCeJGLMztsOcBCSeffitLULSOS0gmtwzPcRhp\nJWGABgZ578/5zXK6b4c1DVLSS8jTZbKSPMb+I+1deqsPDGmLJIxKhuQ2eQTjgDgj\n1JPt6001sgs1qzpfh7pwLEn+HoTXoF7grtA6VzHgCLFtK4GOg59K3r+cB2H+TWa0\nTZa3MS/jEjA4zt6ZqhlQ2BkYHbpV+5kDBxmsG5vlhJJ59q55bnTA1llA4qZXGO34\n1w91r0kbbhMihuV7/hSQeI3cgtc7cnjKjpTUZA5I7iTaR/SqvlR5ztArGg1t5BlT\nHMo6tGavxX8U6FkbPtSaYkXVQZ3Z5rTtW6DtWElyysN3Sta1k3gEdDRezA0Sc85q\nhd5J4q9jjr2rMvX25JPFU2JFIQqoOT1OaUJGOiD2OKqeczksBxmmT6gkIAJJY9AO\n9TcdjWiIxnFDSjOB1rmLjWpIvvPHD/vHJxVK48QSIxxdo2PukKOapKTC6R1cj/MS\ne/rVZgkrbWwfwrBt9bkkJMu1wD1Q8/lWvYzx3Byj5HpRZ31G2mb2nx+Wm3J2Y49q\n3bfDNtxxWLazBI+etatjLub2roic0jhfiPp6xtHKF+Vuv071yXhmQ213dWshYoF+\ndfpyGX3HWvT/AB9bCXToZMDIJ7V5/wDYnj0u5ktlV7hYwE/eeXIgzwDnh19O4qlo\nyHqI10sms2KrKrFZRhum7nj9BXrXhO6W40aMA52HH4f5xXz/ACWmt6Yi6hLAzRhg\nTIOVT0+lerfDHUzParDIx/eoe/Vl6/pUbNMbT2Z6YDkUtMXNPrUgWlpKWgArRrOr\nQpgZ1h/yC7L/AK94/wD0AVPUFh/yC7L/AK94/wD0AVOaAG0lLRSAhn/1LgdSpr5+\n+J9/9q1iK2B/1f8AXivf7pwlvIxH8Jr5i8X3AuPE9y4OQjYFZy+JFLY7G21Fre0g\n0eC2TyYk2M5PJyO1U7WFotPEH71X3ESqMAMc/nVq1jIuElfoyhj+NWp7YpK8pkGG\n5x0xWVOVjeortHaeCFxpErfxbyDSapMfPcZqx4RUpoG7BG5ziqF/81y596JP3BQX\nvGNe3DxLkHk1y2oyyzkgHaDwfauwmtFm5k6DsKqvpsJGBEB+FYp2N7I53RNH06S5\nZ7s73zxv6Gue1yGK18Rzeag8sOSFUZG3Hykfjiu2k0qPuh/ChNGj2jdzjoGUGtY1\nTKVO7ujkPDNquoa7a/wx+XuuD90Zwcnjgc4rq723bTZmlglSWJMHII3D6jvVuLSo\ngfujA44AFW49JhJyYgfrTlNSWqCMOV7ibUksY7kHPmKGFX7BsIBzxVWdSAsS/dHH\n0FWLbI6Vg2ao2PMAj5rE1J9ykA9aveYduDWdeHI6VV9BWKoCppks7cCJSWxWdp1k\nLuRZ7yZIkcEhA3zfQ+n0rUtiNrxuuVfgg9KWXTohjbCAOvyiqTV72Js2rHnOuRDT\n9bv2A+YA/ZyRuCkkYPPbGR+NGi20N/4iHkxAWxYsyMOi4/xrupbKBsh0Bz/eUGqx\n0uFSQp+U/wB1dpNbe1M3SOa1LTEs74tYsdnUgnIq9pepSQsGljA9dtaf9mLu+WMt\nzyT1FXItLt3jw8QyP4l4NZynctRsWre/WZVaPOPeug0uXLYz1rAgslt12xnK+9a+\nl5SUURk7kySsXvFyeboDMv3o2B6VwSWnm2sKXG9FSTcH3gqB/M/SvSNaTfoMwI/h\nya4QxCV0jZQcHO/HStmZWItO8TaddWWp6fe2phSS3kSI5zuXacZ984NZPw6vTahJ\ni3+plDsPbo36HNZ11CkMN3gEvHE2WzwPaoPBc4jv2iZsJLhDj/a4/nio+yXPdH0a\np3AMCCOx9akFZPh+7N3olrKww+3bIv8AdZTgj8xWqOtbJ3RgOpaQdKWmAVo1nVoU\nwM7T/wDkF2X/AF7x/wDoAqc1Bp//ACC7L/r3j/8AQBU5oASmn0p1MY8gDqaQFHUp\nBHZSMeyk/kM18ralMZ9SupWOS8jH9a+lfF1z9m0K8YEBlgfGT7f/AK6+YpOSx9TU\nL4hvY9J05vO0+1mBzuRRW1NskQKyk7efqK5jwjI0uhopUt5Uu39a6i3Be5IIGwck\n1yvSVjs+KNztvD4xoUYPB5JHpWXdRfvmJ9etbOkD/iW7fcn86z75dprVr3EZx+Iy\nn5bgUgUYwecUuNzHPrTguawOhIYsWT0qQQLn7vPpUipk4zUwVV6Dn1p2E0RpCoxk\nCiYqi7VwDUpB6/lVe4yCB+dDdkSlqUHBL1JEzcjGPemNwcd6sRplagoXn+tQTc9R\nV1cYOeoqpcDg4HSqQMpxja5wO9akPzjms1CM81fg7c896ES1cPsahSvLgnPNNNqn\nOFAq4CT1/SjaCCORVbgkykYflwOKiMQXpx9KvunFVnXB6cUDsMjAHBrQs49syEd6\noIPnFbFimXRvTp71cEZT0NK+AOmSZ5G3kV5wZWj81GADltp45r0m9H+gsPUYrzi/\nVftYnQHEi/MPQ9K0myIK7OS1mbyrG9I43fKPfJrB0a4NveKwPoc/Q1qeKpClukGM\nEyFj7isG1OJk9+KcV7pNV+9Y+i/Cl15i3UeRtcrcp/wIYYfmp/OunH6ivPfBt1+7\nsZQflYmCTn1GR+or0Fe30p03oRLceOlOptOFaEhWjWdWhimBn6f/AMguy/694/8A\n0AVMahsP+QXZf9e8f/oAqY0ANJqNjg564FSe/rUMpAB9hSYHCfEe4MXhu8fcNxUL\nj68cV8/t938a9p+Kd0F0hYs482T05wBXi7dAKzhq2ypHW+CpcwXUB5IZZAP8/Su3\niUoUkXLK56D0rzTw3eix1aHPCzfu3J7Z6frXpdu+1ihPG3A9qwqq0rnRTd4WO30J\nzJYsTx04/CoNRXkmn+HmH2ZgD1p18pO761a1gStJGB3p65xketDINzIenSnRKqjA\n6DisTrWxKgyeasLGR9abGB1qQnnrVJEsQ4A6VVu2VYixxkCrErgD9Kxr+Uu3kj+K\nkxWIYX8yTJ6GtCPgcVVjttoBxjFW4nQOATUdRssiLCZ4qlcjhvpWg0yBOTWdcyRt\n0PNVoRcyWkw2fQ81sWMiSxhu9Zogyr8dafpshjkkhP8ACcikM3NoIp2MCoopd/H5\n1Mxz0q0MhYELVZ84q0yv/FjFVpAKGirEUYy3vW7YrwOKxYADIBj2xXQ2keMelaU0\nc9Rj9Vfy7LPuK82mlZpJuBtSVsZ9Ca7/AMRSiPTySe9ed3TtPcqgx5ZBLe49KKj1\nsTT01OJ8SzGW5WMgfKhZT9TWREcMje4q7rdwJ9WuGjPyodoH0qnGPlBHTgitor3T\nGTvJs9U8GTlrGWFDmWP94vPcc163ayie3SVejKCPyrxDwXdiHWFAziWIfn3r2DQ5\nCLZ7c9YXK9e3b9MVlB2lYcloa1KDSClFbkC1o1nVo0wM6w/5Bdl/17x/+gCpjUNh\n/wAguz/69o//AEAVMTgUANYgA56VVl5jcnr6VO3XJ/Kq1wTtxnn73+f89qhjR418\nVrjdLbRZz1IHtXmLnMld58SpxPryxg5VVzmuDxmWpprQctxejDHbFek6bqEk2jw3\nzBTtIjbLgEtnHA6k9+K81z831Ndh4S1K3+axudoZXLwlvfqPrUVo3jc0ou0rHrXh\nSXfBIp6q1al0uXfPTNYPhSdWuriIEdA38xW7fyLEdzcAkDP16VNP4C5aTMCYbZTj\nvSIakul+bd2qurVm9zpi9C/CQBSl+pquj0ufxp30EyJ3LvtFUrxDFKJ+uODV+JQM\nsRUcoD8sMioeodTHu9UuGj22USyMe5PArN03Wb83TRahZhOflljPB+orZbT4lctG\nNpJqb+zUYHdyaElsPlRVk1HrzxXP3+uXaXAWzsWm5++xwK6JtJUsBuJFSSaYir8g\n5qlpuTylCz1OZ4M3EBifHTORVqwjM05n6D0qP+zRJJmRnYemeK04YxEMAY+lILJD\nkcpLg8VdD7hk/hVd4wwyOopFcgYoTBMtlwU6VRlbtipGk+Xr9aqSydQKspE9llpd\n3ocDNdPAANorndOTL7u1dBbtlq2grI5qr1MLxjNstoYx0Z+fyrgbuaVLC6vFjwsH\n7tiSOuCRx6cda6/xldKL6CAnopbH6V5z4nuYYrZguPtEw2Z77e9Zu7mC0hc5BmLZ\nZjlieamgyQq+3FQ/wk1Na8Fe/NdBzI63QW8q+sJARtJKk5r2bRLgPJGw6ywqT7lf\nlrxC0YQQ2smQNkxyfyr1fw/d824yflcf98uP8RXO3aVzTdHdA5GadUaMDz6ipK6T\nMWtGs6tCmBn2H/ILs/8Ar3j/APQBUzdKhsP+QXZf9e8f/oAqVulAER5bFU7w/u3P\n8qtk4Un/ACTWZqzmLTpXzjj+lZzehSPB/FzNLql7NIBhSEXJ5rkV5bPtmum8XNtv\nWiz8zNvYfXoPy/nXNgEbz6DFTT+EctyNj8w/OgkkZHBpCMufpihuOO5rQhHdfCzV\nLhfGUdvLNI8c0LrhmzggZH8q9j1IjHOCMZ5r538Kaj/ZXifT7xjhEmAf/dPB/nX0\nNqv+pBB49aiWhrDVmO8iyJt9KrEFHwRTJATkhtp9aIZ3mRo5QPNTo3qK5r3OlXRM\nv+cVKMCq6Nmns+1c1I2yVpMDHFRkgjA71n/bVe4aMH7vWpWuUjXJcD8aBJ6k+wVM\nrZHpWW2q2ycK241XOv4bakYI+tNLU1UJM2Gbn+tPbmINjFY661uXJiGe2DVebW5y\nvCgKKqw/ZSNge1AbBJzWAuvFR88YP41Out2z8Fih96VjOVOSNxZx0JpW+9kVjm+j\nIDLICPXNadrIJI+oP0qWRcViQM1WOWfFXJBgVXA+bIFVEq5o2pCQnsSMZrVsX3H6\ncGsVW+UAdq1rEiOJ5WOFClmNdEWc8keOfEfWrlfG9zHbTFVhjSMgdM4yf51xsk0t\nw5kmcu5HU1Pq94dS1y9vSc+fM7j6E8fpVfFaWRi22HRalhzt4qI/d/Gprccr6E4o\nEjoUXfpEo3fdw3+fyrvfDN35mnxybsv5eG+ob/8AXXBWQBsmVujAA+3OP610Xgi5\n/wBC2PklXIIz6tXPUXumsdz2m2cvCjA1aBFZmly+ZaRHg5GCfcVpjoK3g7oze46t\nGs6tDmrEZ9h/yC7L/r3j/wDQBUj5J4qOw/5BVl/17x/+gCpO+aQEUnHHUnmsPxJJ\n5djszxsJP8q25D+9x6CuS8ZziOAYPRCTWNV+6y4bniOoMbm5vLuRsgMzAnuc4FY/\nSFq3dXXyYFjAwM7m9/T+prEdT9mP4CnB6A9yBRk7vTrTJOWqwq9MdBz9ahlXBz61\ndySMD5TzX0N4c1RNf8GWV0G3SCMRy+odeD/jXz4VwnPevQfhPrgtr+fRp3xFd/PD\nn/noByPxH8qUtiouzO52jcVbBHTmmyxhZVZatXkfl3B44NVZZVUruOMnArjkrM67\n3HIvzZ9aiu8+XhetWIzzTzCH5xmkxN6nJz6HO6tLBeSRTscn0NZpttQiOyab5s9c\nV3UsSnkDpVeW2jmHzKKakXCST1OMjtpy2GmB+oqz9jugMoI2ral0tMnGaYLR4/uv\nj2JqkztjODRimDV92EhGMf3sUslpftH+8MaevOTW7tnA+/8ArVWaCVlwX61dwujm\n5rSTPNweOwFMWyuZiEhcn8K3o9K3t85JFa9raRQABVANTzGNScFsY2m+FVH7y9nl\nkJ/gDEKK6GxtRaMY0+6OgqwihRzU6AMN361m22czY2Thar9/appmqEHitIksniGT\nioPGGqf2P4KvJFOJZlEMf1Y4/lmrlsuWz715v8UNcF5q1vpETZjtPmkwf4yOn4Cu\niCMZvQ8/AwR7VMRxUYHNTMvy1ZiR9gKngH7sH3qDo+KsQ4KGhgjctSFspj1Kkn8i\nDWj4Sl2XlzA3G0lvqNwrM08h45EOCGBBB9xirWilo9RkkJwzoB+ORn+VYyV00aJ7\nHuHh+XfYgd0bIA/Gt5CCvB69K5HwxNiGIZ4clSfftXVx8MQPrTov3RTWpPWjWcK0\nK2IM6x/5BVl/17x/+gCpW4HHaorH/kGWP/XvH/6AKkl+6T3PAoYIgYfMXbk4yBXD\n+NpM7Rn5Y/ve/X+tdtcMFKcE46gd6858aTkvIv8AG7qnXoO9c9Z+7Y0hueYa82bh\nUyTvOfqKz3iLWkW3q7kn2Aq/qiGW+dmyOyL3PrTrlEtLSKSYAALhYh1Zj/ShOySG\n1q2UBCip5hOEAwuerVT2maUkD5Vq3ukuJN7jnHyoB09KfJELaLaOq8sfVvT8Kq+o\nkjOl++wHQDFMt55bSWO4gcpNCwdGHYjkU9gOnqKjxkN9K0RLPedH1iDxJoUGoxYE\njjEyD+GQDkUSIpO11yOteReCvE7+HtUKSsTY3GFmX+6ezD6fyr1uSVJFV0YMrDIK\nnIIrCrGzNqcroejfNVtD8vXmswPzVyGTIFYGo8kb9uOgqNk5qbINJjn6UhlN1x0z\n7VA5bHI/StTYCenBpphGeFGe9UgMbLZ+7x7CjDMcYrZ8lM4KCoxGu7BjApjbZQSI\n+lWEjC1ZZMdAKYy8c0iWN27uBwKmyqJtHaoi23gVBJITkCgQ2WTLe1Kjbmxmqz5z\n1qaJ0Ub3YKijLEngCtIolsdrGtReH9Cn1CTl1G2JD/E56D+teEmaS6vHnmcvJIxd\n2PUk8mt/xj4ibX9RKxkizt8rCvr6sfr/ACrn7cZLH2rqSsjmk7skVck1MRlabGP3\nhHYGpDwFH+1QBWkBEmaltX5YUkwwx9DUcTbZKOgtmdBphAaMD+I/0q/Y7ftMoJxs\nzz+IrHsZcNEOn7wc+1bFqR9tvT0AwMH3xWUty1sel+HZi1oiDIfYGH1ruoXEixyL\n0Zc/mM15not35NzZ4PDx+nuf8K9C06QeXs7K3FZ0XZ2KmjTHStGs5a0a6jIzrL/k\nFWXr9nj/APQBT5GBK56dT+FN0/8A5Bdn/wBe0X/oApZCd+B34xQwRTuSSwHQsefb\nivLPEV15mszR5DAPkA9uTivUrx/Lj83IwvHpxXjt9cKtze3UnP7zAOPunsB6nJrl\nrPWxrTMGGxJnNxcDnOQpPJ9KxtdnM2osi4KxERqoHBPf9a2dSuhD+6U/NgFh3z2H\n51m2Vg17qsrPxGkhd29B0AHuadPT35BLV2RasrQW1n9qkXMjDEef1NZt027an4mt\n+9Ik2qAFjj+XA6AVz1znJPRnPHsKmDu7spqyKI+Zy3boKjQfL9anddi8e9MxgfiK\n6EZMoYw1eh+DNbcWX2O4bKRnEbH+Een0rz6TiQ/Wt7wxKPtUsZPBANKqrxCm7SPV\nQ6sODU8T4rnre4eDCtkr2rTiulZQQRzXHozqNlHBqUEGsuOcZzmraS56UWGWx945\n6U8DuD2qt5nvTlmAPJpoZY2/N1+XHSmugA96i88etMecetUAE7VAJPA796jL0xpc\n98moXkxxmkSwdyTxSM2I8dzVd7lEFVJrz36d6tKxDZYZwCSxwPrXNa9qzzwPbwki\nJR8x/vf/AFqmu7uS4zGhO09ff/61ZV7Ftt2XvjmhS1Ja0OGkPP1p8HA471Cx+Y+1\nTQ9q6znLKfe+pp8vUj0IqKNv3rVNcn997MKnqV0GSLujDDqKp8hwauQPlACM1DPF\ntYEdDyKYn3Llm2WQdt6k/mK3omH2y8YdXkH5ZH+FczaviQA9Nw/nW7FIftmF5y2C\nPXg1ElqUjq7acJd2irldmVOPrXqGjzCWBJAfvop/of1rxoGSOEyM3STr6AjpXp/h\nm7MltGhHIGc/Uf5/OudaSTNHqjskNadZUZzyD1GRWpn2rrTMSjYf8guy/wCvaL/0\nAUScbiOucCksctpdlzx9nj/9AFK2PM56ryc02CMvxA4i0K6b0TFeNTKBA93KcIHa\nQKe7A/Ln6da9X8VsX0hoQTukbaPYY615D4kuFM62MRASOPBHuRgE/ia5KjvUUUbQ\n0jcwrc/aryB2B3SymRs/wouMf1P41qxbbWx+zwqPOcB2b3PT/H6Y9ao2Cxqskjj9\n1GuMHq3POfbA/GpNMmlv72S6YZRGMijtkngf59Kqe3kiY7luWMCJlJyFzuz+tYly\nC03mN3/QV0VyVtbWNHA3OQWJ9Cf8c1i3cZWKPI5UsrfUGs6b6msjJuV2njsP51HJ\nxGnvipr7/WMB/eFVrhuEX0rqjsYMqTD94frWn4dfGphf7ymsqY5er+gtjV4vcGnP\n4WTF+8elQDdGFbkUrQvGdyH8KLYfuweoq6q+ledc7iml268NkVbhv8fxVHJCrDp1\nqo9qy8qeKaYjaW/BHWlF6Mda5/Lqccil8x/72faqQjfN6uOufxppuR3P61h7z/eN\nIZJP7xqguzYe9VRkkVUlvic45rOJJ6nmnKjv0GB60XESvcHqxqE+ZNyRhfSp0t1H\nJ5PvUmwkUrhYq+UAOB9TVG/TELD25rY2cYArO1FAIiB2oT1E9jzN+JGH+0amj4Gf\nao5xtu5R/tmnbsJmu85SWE8gn0NSTtkI3oMVBEex7VI2TF+lSHQRG2uBnrzVwp5i7P74ymfX0rOc4ZT7VoQ7ZothPUfKfQ0M\nEVoh+8x/EDgjvWrZzbbsNkHG9vyFZUjEzBmGGyN31qWOcqzdyQV/Ohq4JnUQSmW3\n8s5JkuQvtnYSPwrvvB9+WjQ7jtUmMnjoK89sZQbAvjgXAb8lNdF4JuNqyxk5/et8\nv+feuaa0uao9jtmyuAQMDI9MVr7n9B+dc9pkhktkG7LL8uf89q3t/wDsGt4O8bmb\nWpBYHGlWZ/6do/8A0AU4c7vfio7HnSrL/r3i/wDQBUd5dpZ2bzOcAZx71cnbcEjk\nvGN4kMiQLyc7sA/gP1rxjV5Cbu4LNl5XySOdqrXa69qkt20jMxDSZfj0Gdorzq/m\n3BzyQzCMc9h1/OuWn703I1lpGxOZf9DmCkr5iqiAnt/+quh0G22aZEojwzkP07fw\nj+v41y9uBPMnmcKxHA9Mc4+gFddHc/YdNMz8NIQkQz0XjJqq12rImG9zF8QXayPJ\n5X+rVgEHqF4H65NSTATWJkJ6Sq5P1AyPzrFnlMt2U5wqgf1NasZ8zQpMEbgQAPw/\n+tQ48qSGpXuZF0d08h9G6VQmbLZz04qzM4EspPdzxVN8lc9ya3RmyFjls1raHbP9\nsjnYYA6D1qOw0ppmDyD5ewrpLW2CyrgcCoqTSVkVCGt2dTZf6sVfCccVRshhQDWo\ng4rgZ1EW3jkUwpmrezNIYgfagDPaAHsKjNon92tIwnsaaYz6UwM37Ivv+dJ9kQc9\nfrWkYye1MaLPTNMCgIB2GKeI8VYkR024jZiTjjt708W/OWJPtTEVApz0zUqwEnLD\nj0FWvKA6AU7yzjpRcCpIoxgVkX8eY29a3pVAB4rMuotynNCYjzjUtPZpnljHzZ5H\nrWWQVJRgQwOCD2NdvdWZDscday77SVuJZGVdrZ6iuyE1bU55Q10OdjPb3qwOUYev\nNE1hPbP8y/L602I8Y/CtLoldiN+VqSzm8t8H8Kif+vNRBiGBHana6JvqaN3HhzIm\ndj9R6GqgfEgP41etnFxEYicZGR9azSCrkHqDzUrsNnSWE4/suYH++D+Zrf8ACc5W\n6UBj85Lex6Y/9Bri7OcpBLHk/OMVv6BcCF3c/djC7frzWc46MuL1PctCl3koD7df\nxH9a6zzx/dH5159otwDb2tyhwB+7b69Vz+HFdt/aMVRTkkrMclqOszjTLIf9O0f/\nAKAK5HxlfERG33EKqZYD1P8A9b+ddXasBotoc4/0aMZ9PlFeceKrsOSSzBZpDhQO\nWUf/AKqdd6W7hTWpxuuXRi2ybQpEfAA6ngCuPmxJKAoyqDPHqew981q6zcSXN2y8\n5Vd20dvQfrVVbRY1kklPyqcnHQew9/elTXLHUcndi2cAeSDecLnYAD75/LmptV1P\nz54Ej+4jFio/ur0/lWeb/wAmO4OcSSLtU/3PYfhVHe0iyyKT8x8tfx7fkP1rVRu7\nszb6FlgVeaX+8Bt/GtC1l26bJH12yKaz3+WKNG6RjBPqf8/yqWCTNrJjgGQClLUq\nOhTucGaRc/xEikiA3qW5APNSMgaQt+dGMH/GrM2zsrG1QwI6gEMMjFXoLXDscd6y\nfDV6GH2d2zjoDXWiEAAjoa46iadjqg00MgQoRWlFyBUMcYIqZRtNYGiLAHHFO2Z+\ntNRhU45poCLZ6UYx2FWlTP0oMNUkIqf8BppXPYVd8jimmDFUBR2k9qUR1bZMDpUR\npAQhB6UjcLTncDrUJYkVIEUh3cVVliBGKuIBIu5SGB6EHih4+PemI5+8iVUyQOen\nFV1tSFyy8tya02jFzeEEfJGcfj/n/PWrf2YEA4rVuysTu7mDLpkc8eGXP4VhXvhl\nw2+A/hXeC3A7VXvmhtLV55uFRcmnGbWwSSPK7iyaCVo5eo6j0qqbVucEVo3MrXNz\nJMw5dicelMC+1dSbscjM5WeJ8HINOkk807j971q3NEHXB61UNs3UHNVcB9u2HUZ7\n1qW03lyGIHCvnP8ASsdQyONykc1oOvyRTD7mdpNRIuJ6/wCCboahYXVmSfMVMpns\nwOf0ya6n+1x/zxP5GvL/AATqJstajO4bZlDHHcHhhXtm639F/KublT0vY2K97dG3\n8O2iqdrvBEiD1JUAf59q4G7to9W8SzRs2bPTbf8AeAnOWx0z7nP4Vrax4hiitLHb\nlligTA/vHYBXDz6nqrRTJb/6NFM292bgsf8AD8KJS5p6CXurUybi3htzJPdzbS7Z\nIx8x9gKxL+8luMCKPbEDhAR09z71oy25DF5rp5Gbrt4z+PWqcrRqcIBgHgnk1pFE\nOaZk/ZHfls4Hc1ZVYoY41XkjLHHcmpZCGPzc49ahKDtWurIuivIxdgOiipgwS2CD\nruzSbcc/lTQPxoFckT7vNDDvSLxUh9qBD7S4ktLqOeP7yNkZ7+1em6feRX1lHLEc\nqw6HqPb6ivLcEHPT3ra0DVmsJ/Kc/uZD37H1rOrHmRpTlZnpMPPBqdo8jgVQtrlZ\nIwynOauLPxjriuL1OpCDIqxGeBVfzQx+7zUkZFIovKePepA1V1Jx7VIMnpWiJsSb\nuajYn1pfqTTcUwI2PaoHJqwVzzmonX1qWMqNk05Ytw5pSD6Uu8qMAcUhDkhSGIRx\nqERRgADgVDLlUJUZYjAA7mleQ96yLmRr6cQpny1PP+P+H5+9XFXYnoWbKECHJxuY\n5JHvVsAKO1QqNiKqjgDijeRye1End3ElZEjMqqSTgAc1574m1s6jc+RCx+zRnr/e\nPr9KteIvETTl7O1bEfSRx/F7D2rmFGT/AIVvThbVmFSd9EIF9qlCjqRxSquOlNdv\n7vTtWxkMYA88UAY4wKT1oz70wLKMmPmRT6girQtrSeLyiSgOD8p6Vng+/WpFkbg8\nfWpauBuWVibWe1mt7lZDDJnB4JU4yK9E/tO3/wCgiv5V5Ml20eCDhhzxU/26b++K\nylTuWptHQyeIAbeIKAGEagseW6Vj3WqPM5+bI9ayRIxiXnjAFNYsOtWoJEE0s7MO\nSTVcsaDkjrTcHPWrAM8+1HQ9vwoxRhvagBvtQV4/ClHPel5waAI8U9eetLt/lSbT\nwaAHYpuCOnX1NPAyOaQrk80AdJ4c1nypBbytkdFOevt9fT/9Vd5CqyIrq25WGQa8\nfTdu3A7dp4xXpPhK/e8t0VxywOfqOp/GsKsFub0pvY6AR47U/wAoEVaEBAzkUnkH\nPUVhynRchUFakVvanGFx3FAjYnqKLAAI70E+lO8lh3FBjYd6LisM/Smbc1MIjntT\n/IYjqtFhlNo+mKaYj3q+Lc+1Btz6inYRi3+ILRiB8zHaPqajsrPybcAj5m5NWb2J\npdRgt8jhTIfp0/yCKvfZyoHTiqasrE9SgYtqktgADJJrg/EfiUTM9nYMBERh5cfe\n9h7VJ4s8TTXE0um2waGGNykjZ5k9vpXI7e3UGtadPqzGpU6ITryBg1IMKMfnSbcL\nxTCSeM1sYji46c+3NMJ/CjB7mjBpgH6+9AODx2pMUuMjpQAvSlB45zSAcUuCaAF9\nPSn4FR4NLg+tID//2Q==";

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
