using System;
using System.Data;
using System.Windows;
using System.Windows.Controls;

namespace WpfRoosterMaker
{
    /// <summary>
    /// Interaction logic for Create.xaml
    /// </summary>
    public partial class CreateWindow : Window
    {
        public CreateWindow()
        {
            InitializeComponent();
            Load();
        }

        private void Load()
        {
            foreach (DataRow row in MainWindow.dt_klassen.Rows)
            {
                cbKlas.Items.Add(row["klas"]);
            }
            var time = DateTime.Now;
            dpFrom.SelectedDate = time;
            for (int i = 0; i < 24; i++)
            {
                cbHoursFrom.Items.Add(string.Format("{0:D2}", i));
                cbHoursTo.Items.Add(string.Format("{0:D2}", i));
            }
            for (int i = 0; i < 60; i += 5)
            {
                cbMinutesFrom.Items.Add(string.Format("{0:D2}", i));
                cbMinutesTo.Items.Add(string.Format("{0:D2}", i));
            }
            cbHoursFrom.SelectedItem = string.Format("{0:D2}", time.Hour);
            cbHoursTo.SelectedItem = string.Format("{0:D2}", (time.Hour + 1) % 24);

            cbMinutesFrom.SelectedItem = "00";
            cbMinutesTo.SelectedItem = "00";

            dpFrom.SelectedDate = MainWindow.SelectedWeek != default ? MainWindow.SelectedWeek : DateTime.Now;
            cbKlas.SelectedItem = MainWindow.SelectedKlas;
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {
            string lesnaam = tbLes.Text;
            string klas = cbKlas.Text;
            DateTime selDate = dpFrom.SelectedDate ?? DateTime.Now;
            string fromdate = selDate.ToString("yyyyMMdd");
            string teacher = tbDocent.Text;
            string room = tbLokaal.Text;
            string fromtime = (cbHoursFrom.Text ?? "00").PadLeft(2, '0') + (cbMinutesFrom.Text ?? "00").PadLeft(2, '0');
            string totime = (cbHoursTo.Text ?? "00").PadLeft(2, '0') + (cbMinutesTo.Text ?? "00").PadLeft(2, '0');
            MainWindow.Create(lesnaam, klas, fromdate, teacher, room, fromtime, totime);
            Console.WriteLine(lesnaam);
            Console.WriteLine(klas);
            Console.WriteLine(fromdate);
            Console.WriteLine(fromtime);
            Console.WriteLine(totime);
            this.Close();
        }
    }
}
