using MySql.Data.MySqlClient;
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
    /// Interaction logic for Update.xaml
    /// </summary>
    public partial class UpdateWindow : Window
    {
        public UpdateWindow()
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


            DataTable dt_selected = new DataTable();
            MySqlConnection connection = MainWindow.Connect();
            MySqlCommand cmd = new MySqlCommand("SELECT * FROM `rooster` WHERE `id`=@id", connection);
            cmd.Parameters.AddWithValue("@id", MainWindow.SelectedListBox.Tag);
            Console.WriteLine(MainWindow.SelectedListBox.Tag);
            connection.Open();
            dt_selected.Load(cmd.ExecuteReader());
            connection.Close();

            foreach (DataRow row in dt_selected.Rows)
            {
                Console.WriteLine(row["klas"]);
                cbKlas.SelectedItem = row["klas"].ToString();
                tbLes.Text = row["les"].ToString();

                cbHoursFrom.SelectedItem = row["begintijd"].ToString().Substring(0,2);
                cbHoursTo.SelectedItem = row["eindtijd"].ToString().Substring(0, 2);

                cbMinutesFrom.SelectedItem = row["begintijd"].ToString().Substring(2);
                cbMinutesTo.SelectedItem = row["eindtijd"].ToString().Substring(2);

                dpFrom.SelectedDate = DateTime.Parse(row["datum"].ToString());
            }
        }

        private void btnUpdate_Click(object sender, RoutedEventArgs e)
        {
            string lesnaam = tbLes.Text;
            string klas = cbKlas.Text;
            string fromdate = dpFrom.SelectedDate.Value.ToString("yyyy/MM/dd");
            string fromtime = $"{cbHoursFrom.Text}{string.Format("{0:D2}", cbMinutesFrom.Text)}";
            string totime = $"{cbHoursTo.Text}{string.Format("{0:D2}", cbMinutesTo.Text)}";
            MainWindow.Update(lesnaam, klas, fromdate, fromtime, totime);
            this.Close();
        }

        private void btnDelete_Click(object sender, RoutedEventArgs e)
        {
            MainWindow.Delete(MainWindow.SelectedListBox.Tag.ToString());
            this.Close();
        }
    }
}
