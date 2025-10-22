using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;

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
            cbHoursTo.SelectedItem = string.Format("{0:D2}", time.Hour + 1);

            cbMinutesFrom.SelectedItem = "00";
            cbMinutesTo.SelectedItem = "00";

            dpFrom.SelectedDate = MainWindow.SelectedWeek;
            cbKlas.SelectedItem = MainWindow.SelectedKlas;
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {
            string lesnaam = tbLes.Text;
            string klas = cbKlas.Text;
            string fromdate = dpFrom.SelectedDate.Value.ToString("yyyy/MM/dd");
            string fromtime = $"{cbHoursFrom.Text}{string.Format("{0:D2}", cbMinutesFrom.Text)}";
            string totime = $"{cbHoursTo.Text}{string.Format("{0:D2}", cbMinutesTo.Text)}";
            MainWindow.Create(lesnaam, klas, fromdate, fromtime, totime);
            Console.WriteLine(lesnaam);
            Console.WriteLine(klas);
            Console.WriteLine(fromdate);
            Console.WriteLine(fromtime);
            Console.WriteLine(totime);
            this.Close();
        }
    }
}
