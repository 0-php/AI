unit demo;

interface

uses
  Windows, Messages, SysUtils, Classes, Graphics, Controls, Forms, Dialogs,
  StdCtrls, WizardNN;

type
  TForm1 = class(TForm)
    Edit1: TEdit;
    Edit2: TEdit;
    Button1: TButton;
    Label1: TLabel;
    procedure FormCreate(Sender: TObject);
    procedure Button1Click(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
    NeuralNetwork : TWizardNeuralNetwork;
  end;

var
  Form1: TForm1;

implementation

{$R *.DFM}

procedure TForm1.FormCreate(Sender: TObject);
begin
  NeuralNetwork := TWizardNeuralNetwork.Create; // создаем нейросеть
  NeuralNetwork.LoadFromWizardFile(ExtractFilePath(paramstr(0))+'sum.nnw'); // читаем параметры НС из файла
end;

procedure TForm1.Button1Click(Sender: TObject);
begin
  with NeuralNetwork do
  begin
    InputValues['s1'] := StrToFloat(Edit1.Text); // устанавливаем входы НС
    InputValues['s2'] := StrToFloat(Edit2.Text); // 
    Compute;				         // рассчитываем с использование НС
    Label1.Caption := FloatToStr(OutputValues['res']); // выводим результат рассчета
  end;
end;

end.
